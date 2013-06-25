<?php
class Model_Budget_Active extends Model_Budget {
    function init(){
        parent::init();
        $this->addCondition('closed',false);
        $this->addCondition('accepted',true);
    }
}
