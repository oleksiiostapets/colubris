<?php
class Model_Reqcomment_Deleted extends Model_Reqcomment_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_deleted',true);
    }
}
