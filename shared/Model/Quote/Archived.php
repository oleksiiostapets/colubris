<?php
class Model_Quote_Archived extends Model_Quote {
    function init(){
        parent::init(); //$this->debug();
        $this->addCondition('is_archived',true);
    }

}
