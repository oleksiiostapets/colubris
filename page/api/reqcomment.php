<?php
class page_api_reqcomment extends page_api_general {

    function init() {
        parent::init();
        $this->m = $this->add('Model_Reqcomment');
    }

}