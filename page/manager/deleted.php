<?php

class page_manager_deleted extends page_deletedfunctions {
    function init() {
        parent::init();

    }

    function page_index() {
    	$this->add('View_DeletedTabs');
    }

}
