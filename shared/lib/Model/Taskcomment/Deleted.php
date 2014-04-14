<?php
class Model_Taskcomment_Deleted extends Model_Taskcomment_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_deleted',true);
    }
}
