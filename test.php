<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 04/11/14
 * Time: 19:04
 */
require_once'./vendor/autoload.php';
require_once 'lib/Tests.php';
$api = new Tests('colubris-test');
$api->run($argv);