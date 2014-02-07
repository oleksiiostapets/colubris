<?php
class Model_User_Deleted extends Model_User_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('organisation_id',$this->api->auth->model['organisation_id']);
        $this->addCondition('is_deleted',true);
    }
}
