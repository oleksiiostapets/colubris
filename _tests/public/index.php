<?php
chdir('..');
require_once'../vendor/autoload.php';
include 'lib/TestApi.php';
$api = new TestApi('ScrapyardTest');
$api->main();