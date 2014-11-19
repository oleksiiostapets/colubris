<?php
include 'vendor/autoload.php';
require_once 'lib/Tests.php';
$api = new Tests('colubris-tests');
$api->run($argv);