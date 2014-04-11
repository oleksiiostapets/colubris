<?php
class Model_Task_Deleted extends Model_Task_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_deleted',true);
    }
}
