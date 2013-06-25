<?php
class Model_Budget_Unconfirmed extends Model_Budget {
    function init(){
        parent::init();
        $this->addCondition('accepted',false);
    }
}
