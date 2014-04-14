<?php
class Model_User extends Model_User_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_system',false);
        $this->addCondition('is_deleted',false);
    }
}
