<?php

class Factory
{
    const TYPE_STREAM_CONTROLLER = "stream-controller";
    const TYPE_DATABASE = "database";
    const TYPE_ROUTER = "router";
    const TYPE_HTTP_PARSER = "httpparser";
    const TYPE_VALIDATOR = 'validator';
    const TYPE_JSON_PARSER = "json-parser";
    const TYPE_DATE_HANDLER = "date-handler";
    const TYPE_STREAM = "stream";
    const TYPE_SIGN = "sign";

    const MODEL_LOGGER_WEB = "model-logger-web";
    const MODEL_LOGGER_API = "model-logger-api";
    const MODEL_LOGGER_STREAM = "model-logger-stream";
    const MODEL_LOGGER = "model-logger-default";
    const MODEL_STREAM = "model-stream";
    const MODEL_SIGN = "model-sign";

    const LOGGER_FILE = 'file';
    const LOGGER_DB = "db";

    const TYPE_METHOD_MAPPING = array(
        self::TYPE_DATABASE => "getDatabase",
        self::TYPE_HTTP_PARSER => "getHttpParser",
        self::TYPE_VALIDATOR => 'getValidator',
        self::TYPE_JSON_PARSER => "getJsonParser",
        self::TYPE_DATE_HANDLER => "getDateHandler",
        self::TYPE_STREAM_CONTROLLER => "getStreamController",
        self::TYPE_STREAM => "getStream",
        self::TYPE_SIGN => "getSign",
    );

    const MODEL_TO_METHOD_MAPPING = array(
        self::MODEL_LOGGER_WEB => "getModelLoggerWeb",
        self::MODEL_LOGGER_API => "getModelLoggerApi",
        self::MODEL_LOGGER_STREAM => "getModelLoggerStream",
        self::MODEL_LOGGER => "getModelLogger",
        self::MODEL_STREAM => "getModelStream",
        self::MODEL_SIGN => "getModelSign",
    );
    const LIBRARY_TO_TYPE_MAPPING = array();

    const LOGGER_TO_METHOD_MAPPING = array(
        self::LOGGER_DB => "getDbLogger",
        self::LOGGER_FILE => "getFileLogger",
    );
    private static $instances = array();

    /**
     * @param string|null $type
     * @return LoggerFile|LoggerDb
     */
    public static function getLogger(?string $type="") {
        if($type === "") {
            $type = Config::get(Config::LOG_DRIVER);
        }
        if(!array_key_exists($type, self::LOGGER_TO_METHOD_MAPPING)) {
            return null;
        }
        if(!array_key_exists($type, self::$instances)) {
            $logger = call_user_func([new self(), self::LOGGER_TO_METHOD_MAPPING[$type]]);
            self::$instances[$type] = $logger;
        }

        return self::$instances[$type];
    }

    /**
     * @param string $type
     * @param bool $singleton
     * @return Database|Validator|DateHandler|Stream
     */
    public static function getObject(string $type, bool $singleton=false) {
        if(!array_key_exists($type, self::TYPE_METHOD_MAPPING)) {
            return null;
        }
        if($singleton === true) {
            if(array_key_exists($type, self::$instances)) {
                return self::$instances[$type];
            } else {
                $object = call_user_func([new self(), self::TYPE_METHOD_MAPPING[$type]]);
                self::$instances[$type] = $object;
                return $object;
            }
        }

        return call_user_func([new self(), self::TYPE_METHOD_MAPPING[$type]]);
    }


    /**
     * @param string $modelType
     * @return LoggerWebModel|LoggerApiModel
     */
    public static function getModel(string $modelType) {
        if(!array_key_exists($modelType, self::MODEL_TO_METHOD_MAPPING)) {
            return null;
        }
        if(!isset(self::$instances[$modelType])) {
            $object = call_user_func([new self(), self::MODEL_TO_METHOD_MAPPING[$modelType]]);
            self::$instances[$modelType] = $object;
        }

        return self::$instances[$modelType];
    }

    /**
     * @param string $libraryType
     * @return object
     */
    public static function getLibrary(string $libraryType) {
        if(!array_key_exists($libraryType, self::LIBRARY_TO_TYPE_MAPPING)) {
            return null;
        }

        return call_user_func([new self(), self::LIBRARY_TO_TYPE_MAPPING[$libraryType]]);
    }

    private function getSign() {
        return new Sign();
    }

    private function getModelSign() {
        return new SignModel($this->getValidator());
    }

    private function getModelLoggerStream() {
        return new LoggerStreamModel($this->getValidator());
    }

    private function getStream() {
        return new Stream($this->getValidator(), self::getLogger());
    }

    private function getModelStream() {
        return new StreamModel($this->getValidator());
    }

    private function getStreamController() {
        return new StreamController($this->getValidator(), $this->getStream());
    }

    private function getExceptionHandler() {
        return new ExceptionHandler(self::getLogger());
    }

    private function getDbLogger() {
        return new LoggerDb();
    }

    private function getFileLogger() {
        return new LoggerFile();
    }

    private function getModelLoggerWeb() {
        return new LoggerWebModel($this->getValidator());
    }

    private function getModelLoggerApi() {
        return new LoggerApiModel($this->getValidator());
    }

    private function getModelLogger() {
        return new LoggerModel($this->getValidator());
    }

    private function getDatabase() {
        return new Database();
    }

    private function getHttpParser() {
        return new HttpParser();
    }

    private function getValidator() {
        return new Validator();
    }

    private function getJsonParser() {
        return new JsonParser();
    }

    public function getDateHandler() {
        return new DateHandler();
    }
}