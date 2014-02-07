<?php
class Model_User_Admin extends Model_User {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_admin',true);
    }
}
