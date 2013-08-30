<?php
class page_client_quotes extends Page_Quotes {

    public $role = 'client';

    // CRUD config for this role
    public $allow_add  = false;
    public $allow_edit = false;
    public $allow_del  = false;
    public $allowed_actions  = array(
            'details',
            'edit_details',
            'approve',
    );
    //public $form_fields  = array('project_id','name','general','rate','currency','duration','deadline','status');
    public $grid_fields  = array('project','user','name','estimated','estimpay','spent_time','rate','currency','durdead','status');

    function init() {
        parent::init();
        // show only client's quotes
        $pr = $this->quote->join('project','project_id','left','_pr');
        $pr->addField('pr_client_id','client_id');
        $this->quote->addCondition('pr_client_id',$this->api->auth->model['client_id']);
    }
}
