<?php
/**
 * Created by Vadym Radvansky
 * Date: 7/8/14 1:58 PM
 */
trait Helper_Url {

    protected function getParameter($name) {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }
        return false;
    }
    protected function checkGetParameter($name) {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }
        echo json_encode([
            'result' => 'error',
            'error_message'   => 'no '.$name.' parameter',
        ]);
        exit();
    }
    protected function checkPostParameter($name) {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }
        echo json_encode([
            'result' => 'error',
            'error_message'   => 'no '.$name.' parameter',
        ]);
        exit();
    }
}