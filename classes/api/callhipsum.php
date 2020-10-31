<?php


class CallHipsum extends Call
{
    const GET_IPSUM = "get-ipsum";

    protected $apiName = "hipsum";
    private $endpoints = array(self::GET_IPSUM => "https://hipsum.co/api/");

    public function getIpsum(?array $params=array()) {
        if(empty($params)) {
            $params = array(
                'type' => "hipster-centric",
                "sentences" => 3,
            );
        }
        $endpoint = $this->endpoints[self::GET_IPSUM];
        return $this->callApi($endpoint, $params, false);
    }

    protected function processCallResult(string $result, $info) {
        $result = parent::processCallResult($result, $info);
        if(!isset($result["error"])) {
            return $result[0];
        }

        return $result;
    }
}