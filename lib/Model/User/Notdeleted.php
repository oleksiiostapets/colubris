<?php
class Model_User_Notdeleted extends Model_User_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_deleted',false);
    }
}
