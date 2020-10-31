<?php


class CallAggregator extends Call
{
    const SEND_STREAM = "send-stream";

    protected $apiName = 'aggregator';
    private $endpoints = array(self::SEND_STREAM => "aggregator/stream/input");

    public function sendToAggregator($params) {
        $endpoint = $this->endpoints[self::SEND_STREAM];
        return $this->callApi($endpoint, $params, true);
    }

    protected function processCallResult(string $result, $info) {
        $response = parent::processCallResult($result, $info);
        if((int)$response["error"] === 1) {
            $decoded = json_decode($result, true);
            $response["message"] = $decoded["message"];
            //in case of error we need to log outside of transaction, so populate here and use later on
            $response["api_name"] = $this->apiName;
            $response["raw_result"] = $result;
        }

        return $response;
    }
}