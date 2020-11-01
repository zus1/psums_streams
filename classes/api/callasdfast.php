<?php

namespace PsumsStreams\Classes\Api;

/**
 * Class CallAsdfast
 * @package PsumsStreams\Classes\Api
 *
 * Handles calls to ADsFast api
 * http://asdfast.beobit.net/docs/
 *
 */
class CallAsdfast extends Call
{
    const GET_IPSUM = "get-ipsum";

    protected $apiName = 'asdfast';
    private $endpoints = array(self::GET_IPSUM => "http://asdfast.beobit.net/api/");

    public function getIpsum(?array $params=array()) {
        if(empty($params)) {
            $params = array(
                'type' => "word",
                "length" => 20,
            );
        }
        $endpoint = $this->endpoints[self::GET_IPSUM];
        return $this->callApi($endpoint, $params, false);
    }

    protected function processCallResult(string $result, $info) {
        $result = parent::processCallResult($result, $info);
        if(!isset($result["error"])) {
            return $result["text"];
        }

        return $result;
    }
}