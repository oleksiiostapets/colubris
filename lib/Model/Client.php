<?
class Model_Client extends Model_Client_Base {
    function init(){
        parent::init();

        $this->addCondition('is_deleted',false);
    }
}
