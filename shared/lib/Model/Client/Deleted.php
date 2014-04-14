<?php
class Model_Client_Deleted extends Model_Client_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_deleted',true);
    }
}
