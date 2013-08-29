<?php

class page_client_quotes_rfq_requirements extends Page_Requirements {
    public $role = 'client';
    public $requirements_rights = array('allow_add'=>false,'allow_edit'=>true,'allow_del'=>true);
    public $edit_fields = array('name','descr','file_id');
}
