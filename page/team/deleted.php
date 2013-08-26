<?php

class page_team_deleted extends page_deletedfunctions {
    function init() {
        parent::init();

    }

    function page_index() {
    	$this->add('View_DeletedTabs');
    }

}
