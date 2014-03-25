<?php
chdir('..');
include 'vendor/autoload.php';
require_once 'lib/Frontend.php';
$api = new Frontend('colubris');
$api->main();
