<?php
class Model_Budget_Old extends Model_Budget {
    function init(){
        parent::init();
        $this->addCondition('closed',true);
        $this->addCondition('accepted',true);
    }
}
