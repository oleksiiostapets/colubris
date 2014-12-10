<?php
chdir('..');
include 'vendor/autoload.php';
require_once 'lib/Frontend.php';
$api = new Frontend('c43');
$api->main();
