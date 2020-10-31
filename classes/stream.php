<?php


class Stream
{
    const STREAM_FAST = "asdfast";
    const STREAM_BACON = "baconipsum";
    const STREAM_HIPSUM = "hipsum";
    const STREAM_META = "metaphorpsum";

    private $validator;
    private $logger;
    private $sign;

    private $streamResponses = array();

    public function __construct(Validator $validator, LoggerInterface $logger, Sign $sign) {
        $this->validator = $validator;
        $this->logger = $logger;
        $this->sign = $sign;
    }

    public function getModel() {
        return Factory::getModel(Factory::MODEL_STREAM);
    }

    public function getStreamResponses() {
        return $this->streamResponses;
    }

    public function getInputModel() {
        return Factory::getModel(Factory::MODEL_STREAM_INPUT);
    }

    private function getSettingsForStream(string $streamId) {
        $db = Factory::getObject(Factory::TYPE_DATABASE, true);
        return array(
            "active" => (int)$db->getSetting("stream_active_" . $streamId, 1),
            "delay" => (int)$db->getSetting("stream_delay_min_" . $streamId, 5) * 60,
            "tp" => (int)$db->getSetting("stream_throughput_" . $streamId, 5),
            "cache_key" => "cache_key_" . $streamId
        );
    }

    private function getApiObject(string $streamName) {
        return array(
            self::STREAM_FAST => Factory::getApi(Factory::API_FAST),
            self::STREAM_BACON => Factory::getApi(Factory::API_BACON),
            self::STREAM_HIPSUM => Factory::getApi(Factory::API_HIPSUM),
            self::STREAM_META => Factory::getApi(Factory::API_META),
        )[$streamName];
    }

    public function doCycleStreams(array $streams) {
        foreach($streams as $stream) {
            try {
                $id = $stream["stream_id"];
                $name = $stream["name"];
                $streamSettings = $this->getSettingsForStream($id);
                if((int)$streamSettings["active"] === 0) {
                    throw new Exception(sprintf("Deactivated: %s(%s)", $name, $id));
                }
                if(!Cache::shouldIRun($streamSettings["cache_key"], $streamSettings["delay"])) {
                    throw new Exception(sprintf("On timeout: %s(%s)", $name, $id));
                }
                $streamData =$this->handleStreamApiRefresh($id, $name);
                $this->handleSendToAggregator($id, $name, $streamSettings["tp"], $streamData);
            } catch(Exception $e) {
                $this->streamResponses[] = sprintf("%s(%s) returned %s", $id, $name, $e->getMessage());
                continue;
            }
            $this->streamResponses[] = sprintf("%s(%s) returned OK", $id, $name);
        }
    }

    private function handleStreamApiRefresh(string $streamId, string $streamName) {
        //TODO Handle logging
        $returnStream = "";
        $callObject = $this->getApiObject($streamName);
        $result = $callObject->getIpsum();
        if(!is_array($result)) {
            $existingStream = $this->getInputModel()->select(array("input"), array("stream_id" => $streamId));
            if(!$existingStream) {
                $returnStream = $result;
                $this->getInputModel()->insert(array(
                    'name' => $streamName,
                    'stream_id' => $streamId,
                    'input' => $result
                ));
            } else {
                $existingStream = $existingStream[0];
                $currentInput = (string)$existingStream["input"];
                $newInput = sprintf("%s %s", $currentInput, $result);
                $returnStream = $newInput;
                $this->getInputModel()->update(array("input" => $newInput), array("stream_id" => $streamId));
            }
        }

        //lets also get the words stream here, saves us another call do db
        return $returnStream;
    }

    private function handleSendToAggregator(string $streamId, string $streamName, int $tp, string $streamData) {
        if(empty($streamData)) {
            return;
        }
        //first lets get array of word so we can take many as we need. We dont care about format of words here, but we do expect formatted string on input (grammar ok)
        $db = Factory::getObject(Factory::TYPE_DATABASE, true);
        $db->beginTransaction();
        try {
            $streamArray = explode(" ", $streamData);
            if(count($streamArray) <= $tp) {
                $outputString = implode(" ", $streamArray);
                $newInputStr = "";
            } else {
                $needed = array_slice($streamArray, 0, $tp);
                $outputString = implode(" ", $needed);
                $newInput = array_slice($streamArray, count($needed));
                $newInputStr = implode(" ", $newInput);
            }
            $this->getInputModel()->update(array("input" => $newInputStr), array("stream_id" => $streamId));
            $params = array(
                "id" => $streamId,
                'stream' => $outputString,
            );
            $params = $this->sign->addSign($params, $streamId);
            $response = Factory::getApi(Factory::API_AGGREGATOR)->sendToAggregator($params);
            if((int)$response["error"] === 1) {
                throw new Exception("Aggregator call failed");
            }
            $db->commit();
        } catch(Exception $e) {
            $db->rollBack();
            $this->logger->setType(Logger::LOGGER_API)->logApi($response["api_name"], $response["raw_result"], $response["error"], $response["code"]);
            throw $e;
        }
    }
}