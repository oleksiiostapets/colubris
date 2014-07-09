<?php
class Controller_MyCookie extends AbstractController {
    public $login_hash = '_login_hash';
    function init() {
        parent::init();
        $this->app->hg_cookie = $this;
    }
    function generateLoginHash($salt){
        $hash = hash('md5',$salt.microtime(true));
        return $hash;
    }
    function rememberLoginHash($hash,$generate=false) {
        if ($generate) {
            $salt = $hash;
            $hash = $this->generateLoginHash($salt);
        }
        setcookie($this->app->name.$this->login_hash,$hash,time()+60*60*24*30*6);
        return $hash;
    }
    function getLoginHash() {
        return $_COOKIE[$this->app->name.$this->login_hash];
    }
    function forgetLoginHash() {
        setcookie($this->app->name.$this->login_hash,null,time()+60*60*24*30*6);
    }
}
