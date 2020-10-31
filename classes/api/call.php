<?php


class Call
{
    protected $nonJson = false;
    protected $apiName = "api"; //override in child class

    private $defaultOptions = array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_TCP_KEEPALIVE => 1,
        CURLOPT_VERBOSE => 0
    );

    private $customOptions = array();

    private $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    protected function setOption($option, $value) {
        if(!array_key_exists($option, $this->customOptions)) {
            $this->customOptions[$option] = $value;
        }
    }

    protected function callApi(string $endpoint, array $params, bool $post=false)  {
        $ch = curl_init();
        $options = $this->defaultOptions + $this->customOptions;
        if(!empty($params)) {
            if($post === true) {
                $options[CURLOPT_POST] = 1;
                $options[CURLOPT_POSTFIELDS] = http_build_query($params);
            } else {
                $endpoint = $endpoint . "?" . http_build_query($params);
            }
        }
        $options[CURLOPT_URL] = $endpoint;
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return $this->processCallResult($result, $info);
    }

    protected function processCallResult(string $result, $info) {
        $this->logger->setType(Logger::LOGGER_API);

        $decoded = ($this->nonJson === false)? json_decode($result, true) : $result;
        if(json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->logApi($this->apiName, $result, 1);
            return array("error" => 1, "message" => json_last_error_msg(), "code" => json_last_error());
        }

        if((int)$info["http_code"] === 200) {
            $this->logger->logApi($this->apiName, $result, 0, $info["http_code"]);
            return $decoded;
        }

        $this->logger->logApi($this->apiName, $result, 1, $info["http_code"]);
        return array("error" => 1, "message" => "Api error", "code" => $info["http_code"]);
    }
}