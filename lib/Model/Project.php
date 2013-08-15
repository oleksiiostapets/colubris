<?
class Model_Project extends Model_Project_Base {
    function init(){
        parent::init();

        $this->addCondition('is_deleted',false);
    }
}
