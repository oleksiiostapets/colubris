<?php
class Model_Organisation_Deleted extends Model_Organisation_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_deleted',true);
    }
}
