<?php

class page_quotes_rfq_requirement extends Page_Requirement {
    function init() {
        parent::init();

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->api->currentUser()->canSeeRequirements() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }

    }
}
