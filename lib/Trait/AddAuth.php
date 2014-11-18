<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 04/11/14
 * Time: 01:27
 */
trait Trait_AddAuth {
    function addAuth() {
        if (@!$this->auth) {
            $this->add('Auth')
                ->usePasswordEncryption()
                ->setModel('User', 'email', 'password')
            ;
        }
        return $this->auth;
    }
}