<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 17/12/14
 * Time: 23:32
 */




// ---------------------------------------------------------------------------------------------------
//
//  Do not forget to install selenium if you going to test UI
//  http://selenium-release.storage.googleapis.com/index.html
//  And start it before execution the tests
//  java -jar selenium-server-standalone-XXXXXX.jar   //  XXXXX - downloaded version
//
//  More info about selenium you can find here
//  https://code.google.com/p/selenium/wiki/DesiredCapabilities
//
//  Good article
//  http://codeception.com/11-12-2013/working-with-phpunit-and-selenium-webdriver.html
//
//  More examplesof UI testing with selenium
//  https://github.com/DavertMik/php-webdriver-demo/blob/master/tests/GitHubTest.php
//
//  HeadLess Firefox
//  http://www.installationpage.com/selenium/how-to-run-selenium-headless-firefox-in-ubuntu/
//  Start server :::   sudo Xvfb :10 -ac
//
//  http://www.chrisle.me/2013/08/running-headless-selenium-with-chrome/
//  http://alex.nederlof.com/blog/2012/11/19/installing-selenium-with-jenkins-on-ubuntu/
//  http://selftechy.com/2011/08/17/running-selenium-tests-with-chromedriver-on-linux
//
// ---------------------------------------------------------------------------------------------------




include_once 'unit_tests_config.php';
include_once 'vendor/autoload.php';
include_once 'tests/lib/UserInputTrait.php';
include_once 'tests/lib/GetUserTrait.php';