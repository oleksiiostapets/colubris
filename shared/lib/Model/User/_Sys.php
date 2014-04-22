<?php
class Model_User_Sys extends Model_User_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_system',true);
        $this->addCondition('is_deleted',false);
    }
}
