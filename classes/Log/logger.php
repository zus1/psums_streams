<?php

namespace PsumsStreams\Classes\Log;

use Exception;
use PsumsStreams\Classes\HttpCodes;
use PsumsStreams\Interfaces\LoggerInterface;

/**
 * Class Logger
 * @package PsumsStreams\Classes\Log
 *
 * Main logger for tris service. Will log to db or file depending on child called
 * Used driver can be adjusted true config file (.env or init)
 *
 */
class Logger implements LoggerInterface
{
    const LOGGER_API = "api";
    const LOGGER_DEFAULT = "log";

    protected $type = "log";
    protected $availableTypes = array(self::LOGGER_API);

    /**
     *
     * Set type for log driver, to use
     *
     * @param string $type
     * @return $this
     * @throws Exception
     */
    public function setType(string $type) {
        if(!in_array($type, $this->availableTypes)) {
            throw new Exception("Logger not supported");
        }
        $this->type = $type;

        return $this;
    }

    /**
     *
     * Returns settings to use for logger, depending on which driver is in use
     *
     * @param string $type
     * @return array
     * @throws Exception
     */
    public function getLoggerSettings(string $type) : array {
        throw new Exception("If you are here, something is wrong", HttpCodes::INTERNAL_SERVER_ERROR); //needs to be overriden in child class
    }

    /**
     *
     * Handle logging for all api responses
     * Table streams_api_call_log
     *
     * @param string $api
     * @param string $rawResult
     * @param int|null $error
     * @param int|null $code
     * @throws Exception
     */
    public function logApi(string $api, string $rawResult, ?int $error=0, ?int $code=0) : void {
        throw new Exception("If you are here, something is wrong", HttpCodes::INTERNAL_SERVER_ERROR); //needs to be overriden in child class
    }

    /**
     * @param string $message
     * @param string|null $type
     * @throws Exception
     */
    public function log(string $message, ?string $type = "message"): void {
        throw new Exception("If you are here, something is wrong", HttpCodes::INTERNAL_SERVER_ERROR); //needs to be overriden in child class
    }
}