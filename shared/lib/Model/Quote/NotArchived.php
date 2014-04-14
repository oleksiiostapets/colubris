<?php
class Model_Quote_NotArchived extends Model_Quote {
    function init(){
        parent::init(); //$this->debug();
        $this->addCondition('is_archived',false);
    }

}
