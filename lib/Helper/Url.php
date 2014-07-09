<?php
/**
 * Created by Vadym Radvansky
 * Date: 7/8/14 1:58 PM
 */
trait Helper_Url {

    protected function checkGetParameter($name) {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }
        return false;
    }
}