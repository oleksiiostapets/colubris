<?
class Model_Attach extends Model_Attach_Base {
    function init(){
        parent::init();
        $this->addCondition('is_deleted',false);
    }
}
