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
    protected function checkGetParameter($name,$can_by_null=false) {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }
        if (!$can_by_null) {
            return [
                'result' => 'error',
                'error_message'   => 'no '.$name.' parameter',
            ];
        }
    }
    protected function checkPostParameter($name,$can_by_null=false) {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }
        if (!$can_by_null) {
            echo json_encode([
                'result' => 'error',
                'error_message'   => 'no '.$name.' parameter',
            ]);
            exit();
        }
    }
}