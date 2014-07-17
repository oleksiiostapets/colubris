<?php
class Model_Project_Client extends Model_Project {

    function init(){
        parent::init();
        $this->forClient();
    }
    
}
