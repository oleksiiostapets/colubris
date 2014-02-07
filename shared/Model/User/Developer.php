<?php
class Model_User_Developer extends Model_User {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_developer',1);
    }
}
