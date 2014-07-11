<?php
class page_api_task extends Page_api_general {

    function init() {
        parent::init();
        $this->m = $this->add('Model_Task');
    }

}