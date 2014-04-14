<?php
class Model_Quote_Deleted extends Model_Quote_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_deleted',true);
    }
}
