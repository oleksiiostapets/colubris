<?php
class page_api_taskcomment extends Page_api_general {

    function init() {
        parent::init();
        $this->m = $this->add('Model_Taskcomment');
    }

}