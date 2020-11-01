<?php

namespace PsumsStreams\Classes\Api;

/**
 * Class CallMetaphorpsum
 * @package PsumsStreams\Classes\Api
 *
 * Handles calls to Metaphorpsum api
 * http://metaphorpsum.com/
 *
 */
class CallMetaphorpsum extends Call
{
    protected $nonJson = true;
    protected $apiName = 'metaphorpsum';

    const GET_IPSUM = "get-ipsum";

    private $endpoints = array(self::GET_IPSUM => "http://metaphorpsum.com/sentences/{sentences}");

    public function getIpsum(?array $params=array()) {
        $sentences = (isset($params["sentences"]))? $params["sentences"] : 3;
        $endpoint = str_replace("{sentences}", $sentences, $this->endpoints[self::GET_IPSUM]);
        return $this->callApi($endpoint, $params, false);
    }
}