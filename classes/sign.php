<?php

namespace PsumsStreams\Classes;

use Exception;

/**
 * Class Sign
 * @package PsumsAggregator\Classes
 *
 * Class that will add sign key to output streams
 *
 */
class Sign
{
    public function getModel() {
        return Factory::getModel(Factory::MODEL_SIGN);
    }

    /**
     *
     * Adds sing key to stream parameters array
     *
     * @param array $payload
     * @param string $streamId
     * @return array
     * @throws Exception
     */
    public function addSign(array $payload, string $streamId) {
        $sign = $this->getModel()->select(array("sign_key"), array("stream_id" => $streamId));
        if(!$sign) {
            throw new Exception("Sign failed");
        }
        $payload["sign"] = $sign[0]["sign_key"];

        return $payload;
    }
}