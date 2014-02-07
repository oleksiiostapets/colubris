<?
class Model_Taskcomment extends Model_Taskcomment_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_deleted',false);
    }
}
