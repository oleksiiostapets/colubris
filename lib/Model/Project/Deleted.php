<?php
class Model_Project_Deleted extends Model_Project_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_deleted',true);
    }
}
