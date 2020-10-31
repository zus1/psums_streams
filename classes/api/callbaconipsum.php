<?php


class CallBaconipsum extends Call
{
    const GET_IPSUM = "get-ipsum";

    protected $apiName = 'baconipsum';
    private $endpoints = array(self::GET_IPSUM => "https://baconipsum.com/api/");

    public function getIpsum(?array $params=array()) {
        if(empty($params)) {
            $params = array(
                'type' => "meat-and-filler",
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