<?php
class page_quotes extends Page {
    public $quote;
    function init() {
        parent::init();

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->api->currentUser()->canSeeQuotesList() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }
    }

    function page_index() {
        $this->addBreadCrumb($this);

        $this->add('H1')->set('Quotes');

        $tabs = $this->add('Tabs');

        $tabs->addTabUrl('./active','Active');
        $tabs->addTabUrl('./archived','Archived');

    }
    function page_active() {
        $this->addRequestForQuotationButton($this);

        if ($this->api->currentUser()->isDeveloper()) {
            $this->quote = $this->add('Model_Quote_Participant');
            // developer do not see not well prepared (quotation_requested status) and finished projects
            $this->quote->addCondition('status',array(
                'estimate_needed','not_estimated','estimated','estimation_approved'
            ));
        }else{
            $this->quote = $this->add('Model_Quote');
        }
        $this->quote->addCondition('is_archived',false);
        $pr = $this->quote->join('project','project_id','left','_pr');
        $pr->addField('pr_name','name');
        if ($this->api->currentUser()->isClient()) {
            // show only client's quotes
            $pr->addField('pr_client_id','client_id');
            $this->quote->addCondition('pr_client_id',$this->api->auth->model['client_id']);
        }
        $pr->addField('project_name','name');
        $this->quote->setOrder(array('project_name','status'));//->debug();

        $this->addQuotesCRUD($this,'active');
    }
    function page_archived() {
        if ($this->api->currentUser()->isDeveloper()) {
            $this->quote = $this->add('Model_Quote_Participant');
            // developer do not see not well prepared (quotation_requested status) and finished projects
            $this->quote->addCondition('status',array(
                'estimate_needed','not_estimated','estimated','estimation_approved'
            ));
        }else{
            $this->quote = $this->add('Model_Quote');
        }
        $this->quote->addCondition('is_archived',true);
        $pr = $this->quote->join('project','project_id','left','_pr');
        $pr->addField('pr_name','name');
        if ($this->api->currentUser()->isClient()) {
            // show only client's quotes
            $pr->addField('pr_client_id','client_id');
            $this->quote->addCondition('pr_client_id',$this->api->auth->model['client_id']);
        }
        $pr->addField('project_name','name');
        $this->quote->setOrder(array('project_name','status'));//->debug();
        $this->addQuotesCRUD($this,'archive');
    }

    function addBreadCrumb($view) {
        $view->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Quotes',
                    'url' => 'quotes',
                ),
            )
        ));
    }

    function addRequestForQuotationButton($view) {
        if ($this->api->currentUser()->canSendRequestForQuotation()) {
            $b = $view->add('Button')->set('Request For Quotation');
            $b->addStyle('margin-bottom','10px');
            $b->js('click', array(
                $this->js()->univ()->redirect($this->api->url('quotes/rfq'))
            ));
        }
    }

    function addQuotesCRUD($view,$mode) {
        $user = $this->api->currentUser();
        $cr = $view->add('CRUD', array(
            'grid_class'      => 'Grid_Quotes',
            'allow_add'       => false,
            'allow_edit'      => $this->quote->canUserEditQuote($user),
            'allow_del'       => $this->quote->canUserDeleteQuote($user),
            'allowed_actions' => $this->quote->userAllowedActions($user),
        ));
        $cr->setModel(
            $this->quote,
            $this->quote->whatQuoteFieldsUserCanEdit($user),
            $this->quote->whatQuoteFieldsUserCanSee($user)
        );

        if($cr->grid){
            if ($this->quote->userAllowedArchive($user)){
                if($mode=='active'){
                    $cr->grid->addColumn('button','in_archive');
                }
                if($mode=='archive'){
                    $cr->grid->addColumn('button','activate');
                }
                if($_GET['in_archive']){
                    $mq=$this->add('Model_Quote')->load($_GET['in_archive']);
                    $mq->in_archive();
                    $mq->save();

                    $cr->grid->js()->reload()->execute();
                }
                if($_GET['activate']){
                    $mq=$this->add('Model_Quote')->load($_GET['activate']);
                    $mq->activate();
                    $mq->save();

                    $cr->grid->js()->reload()->execute();
                }
            }
            
            $cr->grid->addClass('zebra bordered');
            $cr->grid->add('View_ExtendedPaginator',
                array(
                    'values'=>array('10','50','100'),
                    'grid'=>$cr->grid,
                ),
                'extended_paginator');
            $cr->grid->addQuickSearch(array('quote.name','project'));
        }

    }
}
