<?
class Model_Reqcomment extends Model_Reqcomment_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_deleted',false);
    }
}
