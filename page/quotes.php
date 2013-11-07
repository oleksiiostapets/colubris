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

        $this->addRequestForQuotationButton($this);

        $tabs = $this->add('Tabs');

        $tabs->addTabUrl('./requested','Requested');
        $tabs->addTabUrl('./estimate_needed','Estimate Needed');
        $tabs->addTabUrl('./not_estimated','Not Estimated');
        $tabs->addTabUrl('./estimated','Estimated');
        $tabs->addTabUrl('./estimation_approved','Estimation Approved');
        $tabs->addTabUrl('./finished','Finished');
        $tabs->addTabUrl('./archived','Archived');

    }
    function getBaseModel(){
        if ($this->api->currentUser()->isDeveloper()) {
            $quote = $this->add('Model_Quote_Participant');
        }else{
            $quote = $this->add('Model_Quote');
        }
        $pr = $quote->join('project','project_id','left','_pr');
        $pr->addField('pr_name','name');
        if ($this->api->currentUser()->isClient()) {
            // show only client's quotes
            $pr->addField('pr_client_id','client_id');
            $quote->addCondition('pr_client_id',$this->api->auth->model['client_id']);
        }
        $pr->addField('project_name','name');
        $quote->setOrder(array('project_name','status'));//->debug();

        return $quote;
    }
    function page_requested() {
        $quote=$this->getBaseModel();
        $quote->addCondition('is_archived',false);
        $quote->addCondition('status','quotation_requested');

        $this->addQuotesCRUD($this,$quote,'active');
    }
    function page_estimate_needed() {
        $quote=$this->getBaseModel();
        $quote->addCondition('is_archived',false);
        $quote->addCondition('status','estimate_needed');

        $this->addQuotesCRUD($this,$quote,'active');
    }
    function page_not_estimated() {
        $quote=$this->getBaseModel();
        $quote->addCondition('is_archived',false);
        $quote->addCondition('status','not_estimated');

        $this->addQuotesCRUD($this,$quote,'active');
    }
    function page_estimated() {
        $quote=$this->getBaseModel();
        $quote->addCondition('is_archived',false);
        $quote->addCondition('status','estimated');

        $this->addQuotesCRUD($this,$quote,'active');
    }
    function page_estimation_approved() {
        $quote=$this->getBaseModel();
        $quote->addCondition('is_archived',false);
        $quote->addCondition('status','estimation_approved');

        $this->addQuotesCRUD($this,$quote,'active');
    }
    function page_finished() {
        $quote=$this->getBaseModel();
        $quote->addCondition('is_archived',false);
        $quote->addCondition('status','finished');

        $this->addQuotesCRUD($this,$quote,'active');
    }
    function page_archived() {
        $quote=$this->getBaseModel();
        $quote->addCondition('is_archived',true);

        $this->addQuotesCRUD($this,$quote,'archive');
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

    function addQuotesCRUD($view,$quote,$mode) {
        $user = $this->api->currentUser();
        $cr = $view->add('CRUD', array(
            'grid_class'      => 'Grid_Quotes',
            'allow_add'       => false,
            'allow_edit'      => $quote->canUserEditQuote($user),
            'allow_del'       => $quote->canUserDeleteQuote($user),
            'allowed_actions' => $quote->userAllowedActions($user,$mode),
            'mode'            => $mode,
        ));

        $cr->setModel(
            $quote,
            $quote->whatQuoteFieldsUserCanEdit($user),
            $quote->whatQuoteFieldsUserCanSee($user)
        );

        if($cr->grid){
            if ($quote->userAllowedArchive($user)){
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
