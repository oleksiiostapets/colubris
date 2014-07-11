<?php
class page_api_user extends Page_api_general {

    function init() {
        parent::init();
        $this->m = $this->add('Model_User');
    }

}