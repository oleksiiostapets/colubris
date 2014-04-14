<?php
class Model_Organisation extends Model_Organisation_Base {
    function init(){
        parent::init();
        $this->addCondition('is_deleted',false);
    }
}