<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 19/12/14
 * Time: 17:41
 */
trait UserInputTrait {

    protected function waitForUserInput($message='') {
        if ($this->config->visual_mode) {
            $message = $message . "\nPress <Enter>";
            $this->sendConsoleMessage($message);
            if(trim(fgets(fopen("php://stdin","r"))) != chr(13)) return;
        }
    }
    protected function sendConsoleMessage($message) {
        if ($this->config->visual_mode) {
            fwrite(STDOUT, $message."\n");
        }
    }

}