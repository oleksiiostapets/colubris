<?php
class Model_Attach_Deleted extends Model_Attach_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_deleted',true);
    }
}
