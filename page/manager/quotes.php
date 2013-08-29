<?php
class page_manager_quotes extends Page_Quotes {

    public $role = 'manager';

    // CRUD config for this role
    public $allow_add  = false;
    public $allow_edit = true;
    public $allow_del  = true;
    public $allowed_actions  = array(
            'requirements',
            'estimation',
            'send_to_client',
            'approve',
    );
    public $form_fields  = array('project_id','name','general','rate','currency','duration','deadline','status');
    public $grid_fields  = array('project','user','name','estimated','estimpay','spent_time','rate','currency','durdead','status');

}
