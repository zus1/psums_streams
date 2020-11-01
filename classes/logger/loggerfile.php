<?php

namespace PsumsStreams\Classes\Log;

use Exception;
use PsumsStreams\Classes\HttpParser;
use PsumsStreams\Interfaces\LoggerInterface;

/**
 * Class LoggerFile
 * @package PsumsStreams\Classes\Log
 *
 * Logger class for handling file log driver
 *
 */
class LoggerFile extends Logger implements LoggerInterface
{
    private $rootDirectory;

    public function __construct()
    {
        $this->rootDirectory = HttpParser::root() . "/logs/";
    }

    /**
     * @param string $type
     * @return array
     */
    public function getLoggerSettings(string $type): array
    {
        return array(
            self::LOGGER_API => array("file" => $this->rootDirectory . "api_call.log"),
            self::LOGGER_DEFAULT => array("file" => $this->rootDirectory . "log.log"),
        )[$type];
    }

    /**
     * Creates directory for logs, if it dose not exists
     * Handles ownership of new directory
     */
    private function createLogDirectory() {
        if(!file_exists($this->rootDirectory)) {
            mkdir($this->rootDirectory, 0777);
            $owner = posix_getpwuid(fileowner($this->rootDirectory))["name"];
            $iAm = shell_exec("whoami");
            if($owner !== "www-data" && $iAm === "root") {
                chown($this->rootDirectory, "www-data");
            }
        }
    }

    private function addLine(string $line) {
        $setting = $this->getLoggerSettings($this->type);
        if(!$setting) {
            return;
        }
        $fh = fopen($setting["file"], "a+");
        if(!$fh) {
            throw new Exception("Could not open log file");
        };
        fwrite($fh, $line);
        fclose($fh);
    }

    /**
     * @param string $api
     * @param string $rawResult
     * @param int|null $error
     * @param int|null $code
     * @throws Exception
     */
    public function logApi(string $api, string $rawResult, ?int $error=0, ?int $code=0): void
    {
        $this->createLogDirectory();
        $this->addLine($this->createLogExceptionLine($api, $rawResult, $error, $code));
    }

    /**
     * @param string $message
     * @param string|null $type
     * @throws Exception
     */
    public function log(string $message, ?string $type = "message"): void {
        $this->createLogDirectory();
        $this->addLine($this->createLogMessageLine($message, $type));
    }

    /**
     * @param string $message
     * @param string $type
     * @return string
     */
    private function createLogMessageLine(string $message, string $type) {
        return sprintf("[%s][%s]%s", $type, date("Y-m-d H:i:s"), $message);
    }

    /**
     *
     *Generates single Exception line to be added to log
     *
     * @param $api
     * @param $rawResult
     * @param $error
     * @param $code
     * @return string
     */
    private function createLogExceptionLine($api, $rawResult, $error, $code) {
        return sprintf("[API_CALL][%s]%s (%s:%d)\n%s", date("Y-m-d H:i:s"), $api, $error, $code, $rawResult);
    }
}