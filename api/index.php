<?php
/**
 * Created by Vadym Radvansky
 * Date: 8/4/14 4:23 PM
 */
//include'../vendor/atk4/atk4/loader.php';

include '../vendor/autoload.php';
include 'lib/API.php';

$api = new Api('colubris_api');
$api->main();