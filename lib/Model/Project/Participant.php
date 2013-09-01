<?
class Model_Project_Participant extends Model_Project {

    function init(){
        parent::init();
        $this->participateIn();
    }
    
}
