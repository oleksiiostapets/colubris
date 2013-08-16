<?php

class page_team_deleted extends page_deletedfunctions {
    function init() {
        parent::init();

    }

    function initMainPage() {
    	$this->add('View_DeletedTabs');
    }

}
