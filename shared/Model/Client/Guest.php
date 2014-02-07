<?
class Model_Client_Guest extends Model_Client_Definitions {
    function init(){
        parent::init();
        $this->addCondition('is_deleted',false);
    }
}
