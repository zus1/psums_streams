<?php


class StreamController
{
    private $validator;
    private $stream;

    public function __construct(Validator $validator, Stream $stream) {
        $this->validator = $validator;
        $this->stream = $stream;
    }

    public function cycleStreams() {
        try {
            $availableStreams = $this->stream->getModel()->select(array("stream_id", "name"), array());
            $this->stream->cycleStreams($availableStreams);
        } catch(Exception $e) {
            return $e->getMessage();
        }

        return "ok";
    }
}