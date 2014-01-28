<?php
chdir('..');
include 'vendor/autoload.php';
$api = new Frontend('colubris');
$api->main();
