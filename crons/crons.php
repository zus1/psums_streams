<?php

/*
 * Inits cycle for refreshing streams and sending data to aggregator
 * */

use PsumsStreams\Classes\Factory;

$_SERVER["DOCUMENT_ROOT"] = dirname(__DIR__);
include_once($_SERVER["DOCUMENT_ROOT"] . "/include.php");

$report = Factory::getObject(Factory::TYPE_STREAM_CONTROLLER)->cycleStreams();
echo $report;