<?php
class page_team_quotes extends Page_Quotes {

    public $role = 'team';

    // CRUD config for this role
    public $allow_add  = false;
    public $allow_edit = false;
    public $allow_del  = false;
    public $allowed_actions  = array(
            'details',
            'estimate',
    );
    //public $form_fields  = array('project_id','name','general','rate','currency','duration','deadline','status');
    public $grid_fields  = array('project','user','name','estimated','spent_time','durdead','status');
    function init() {
        parent::init();
        // developer do not see not well prepared (quotation_requested status) and finished projects
        $this->quote->addCondition('status',array(
            'estimate_needed','not_estimated','estimated','estimation_approved'
        ));
    }

}
