<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 22/11/14
 * Time: 00:21
 */

trait Trait_Temp_Proxy {


    public $app;

    function getConfig($a,$b='') {
        return $this->app->getConfig($a,$b);
    }

}