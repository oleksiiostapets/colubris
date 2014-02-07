<?
class Model_Task extends Model_Task_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_deleted',false);
    }

}
