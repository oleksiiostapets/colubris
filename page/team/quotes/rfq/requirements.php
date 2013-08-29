<?php

class page_team_quotes_rfq_requirements extends Page_Requirements {
    public $role = 'team';
    public $requirements_rights = array('allow_add'=>false,'allow_edit'=>true,'allow_del'=>false);
    public $edit_fields = array('estimate');
}
