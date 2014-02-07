<?php
class Model_Task_Base extends Model_Task_Definitions {
    public $table='task';

    function init(){
        parent::init();

//        $this->addField('requester_id')->refModel('Model_User_Organisation');
        $this->hasOne('User_Organisation','requester_id');
//        $this->addField('assigned_id')->refModel('Model_User_Organisation');
        $this->hasOne('User_Organisation','assigned_id');
    }
}
