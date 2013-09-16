<?
class Model_Requirement extends Model_Requirement_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_deleted',false);
    }
}
