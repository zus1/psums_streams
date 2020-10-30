<?php


class Stream
{
    private $validator;
    private $logger;

    public function __construct(Validator $validator, LoggerInterface $logger) {
        $this->validator = $validator;
        $this->logger = $logger;
    }

    private function getSettingsForStream(string $streamId) {
        $db = Factory::getObject(Factory::TYPE_DATABASE, true);
        return array(
            "active" => $db->getSetting("stream_active_" . $streamId, 1),
            "delay" => $db->getSetting("stream_delay_min_" . $streamId, 5),
            "tp" => $db->getSetting("stream_throughput_" . $streamId, 5),
            "cache_key" => "cache_key_" . $streamId
        );
    }

    public function getModel() {
        return Factory::getModel(Factory::MODEL_STREAM);
    }

    public function cycleStreams(array $streams) {
        foreach($streams as $stream) {
            $id = $stream["stream_id"];
            $name = $stream["name"];
            $streamSettings = $this->getSettingsForStream($id);
            if(!Cache::shouldIRun($streamSettings["cache_key"], $streamSettings["delay"])) {
                return;
            }

        }
    }
}