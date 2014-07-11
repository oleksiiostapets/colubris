<?php
class page_api_quote extends Page_api_general {

    function init() {
        parent::init();
        $this->m = $this->add('Model_Quote');
    }

}