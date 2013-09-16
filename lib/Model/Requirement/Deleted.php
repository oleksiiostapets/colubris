<?php
class Model_Requirement_Deleted extends Model_Requirement_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_deleted',true);
    }
}
