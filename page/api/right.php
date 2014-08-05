<?php
class page_api_right extends Page_api_general {

    function init() {
        parent::init();
        $this->m = $this->add('Model_User_Right');
    }
    function canSeeTasks(){
//        return $this->m->canSeeTasks();
        return 'afdgnbf';
    }
}