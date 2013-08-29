<?php

class page_manager_quotes_rfq_requirements extends Page_Requirements {
    public $role = 'manager';
    public $requirements_rights = array('allow_add'=>false,'allow_edit'=>true,'allow_del'=>true);
    public $edit_fields = array('name','descr','estimate','file_id');
}
