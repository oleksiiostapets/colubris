<?php
class page_quotes extends Page {
    public $active_tab=0;
    function init() {
        parent::init();
        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->app->model_user_rights->canSeeQuotes() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }
        $this->title = 'Quotes';
    }

    function page_index() {
        $this->addBreadCrumb($this);

        //$this->add('View_SwitcherQuotes');

        $this->add('H1')->set('Quotes');

        $this->addRequestForQuotationButton($this);
        $this->addArchiveButton($this);

        $tabs = $this->add('Tabs');

        $tabs->addTabUrl('./requested','Requested ('.$this->getModelRequested()->count().')');
        $tabs->addTabUrl('./estimate_needed','Estimate Needed ('.$this->getModelEstimateNeeded()->count().')');
        $tabs->addTabUrl('./not_estimated','Not Estimated ('.$this->getModelNotEstimated()->count().')');
        $tabs->addTabUrl('./estimated','Estimated ('.$this->getModelEstimated()->count().')');
        if(!$this->app->model_user_rights->canSubmitForQuotation()){
            $tabs->addTabUrl('./estimation_approved','Estimation Approved ('.$this->getModelEstimationApproved()->count().')');
            $tabs->addTabUrl('./finished','Finished ('.$this->getModelFinished()->count().')');
        }

        if ($_GET['active_tab']>0){
            $this->api->memorize('active_tab',$_GET['active_tab']);
        }

    }
    function getBaseModel(){
        $quote = $this->add('Model_Quote')->notDeleted()->getThisOrganisation();
        if($this->api->recall('q_project_id')>0){
            $quote->addCondition('project_id',$this->api->recall('q_project_id'));
        }
        if($this->api->recall('q_client_id')>0){
            $pm=$this->add('Model_Project')->notDeleted();
            $pm->addCondition('client_id',$this->api->recall('q_client_id'));
            $ids="";
            foreach ($pm as $p){
                if($ids=="") $ids=$p['id'];
                else $ids=$ids.','.$p['id'];
            }
            $quote->addCondition('project_id','in',$ids);
        }
        $pr = $quote->leftJoin('project','project_id','left','_pr');
        $pr->addField('pr_name','name');
        /*if ($this->api->currentUser()->isClient()) {
            // show only client's quotes
            $pr->addField('pr_client_id','client_id');
            $quote->addCondition('pr_client_id',$this->app->currentUser()->get('client_id'));
        }*/
        $pr->addField('project_name','name');
        $quote->setOrder(array('project_name','status'));//->debug();

        return $quote;
    }
    function getModelRequested(){
        $quote=$this->getBaseModel();
        $quote->addCondition('is_archived',false);
        $quote->addCondition('status','quotation_requested');
        return $quote;
    }
    function page_requested() {
        $this->active_tab=1;
        $quote=$this->getModelRequested();
        $this->addQuotesCRUD($this,$quote,'active');
    }
    function getModelEstimateNeeded(){
        $quote=$this->getBaseModel();
        $quote->addCondition('is_archived',false);
        $quote->addCondition('status','estimate_needed');
        return $quote;
    }
    function page_estimate_needed() {
        $this->active_tab=2;
        $quote=$this->getModelEstimateNeeded();
        $this->addQuotesCRUD($this,$quote,'active');
    }
    function getModelNotEstimated(){
        $quote=$this->getBaseModel();
        $quote->addCondition('is_archived',false);
        $quote->addCondition('status','not_estimated');
        return $quote;
    }
    function page_not_estimated() {
        $this->active_tab=3;
        $quote=$this->getModelNotEstimated();
        $this->addQuotesCRUD($this,$quote,'active');
    }
    function getModelEstimated(){
        $quote=$this->getBaseModel();
        $quote->addCondition('is_archived',false);
        $quote->addCondition('status','estimated');
        return $quote;
    }
    function page_estimated() {
        $this->active_tab=4;
        $quote=$this->getModelEstimated();
        $this->addQuotesCRUD($this,$quote,'active');
    }
    function getModelEstimationApproved(){
        $quote=$this->getBaseModel();
        $quote->addCondition('is_archived',false);
        $quote->addCondition('status','estimation_approved');
        return $quote;
    }
    function page_estimation_approved() {
        $this->active_tab=5;
        $quote=$this->getModelEstimationApproved();
        $this->addQuotesCRUD($this,$quote,'active');
    }
    function getModelFinished(){
        $quote=$this->getBaseModel();
        $quote->addCondition('is_archived',false);
        $quote->addCondition('status','finished');
        return $quote;
    }
    function page_finished() {
        $this->active_tab=6;
        $quote=$this->getModelFinished();
        $this->addQuotesCRUD($this,$quote,'active');
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
        if ($this->app->model_user_rights->canAddQuote()) {
            $b = $view->add('Button')->set('Request For Quotation');
            $b->addStyle('margin-bottom','10px');
            $b->js('click', array(
                $b->js()->univ()->redirect($this->app->url('quotes/rfq'))
            ));
        }
    }
	function addArchiveButton($view){
		if(!$this->api->model_user_rights->canSubmitForQuotation()){
			$b = $view->add('Button')->set('See Archive');
			$b->js('click', array(
				$b->js()->univ()->redirect($this->app->url('quotes/archive'))
			));
		}
	}

    function addQuotesCRUD($view,$quote,$mode) {
        $cr = $view->add('CRUD', array(
            'form_class'      => 'Form_EditQuote',
            'grid_class'      => 'Grid_Quotes',
            'allow_add'       => false,
            'allow_edit'      => $this->app->model_user_rights->canEditQuote(),
            'allow_del'       => $this->app->model_user_rights->canDeleteQuote(),
            'allowed_actions' => array('requirements','estimation','send_to_client','approve'),
        ))->addClass('atk-box');

        $cr->setModel(
            $quote
        );

        if($cr->grid){
            if ($this->app->model_user_rights->canMoveToFromArchive()){
                if($_GET['in_archive']){
                    $mq=$this->add('Model_Quote')->notDeleted()->getThisOrganisation()->load($_GET['in_archive']);
                    $mq->in_archive();
                    $mq->save();

                    $this->js()->univ()->redirect('quotes',array('active_tab'=>$this->active_tab))->execute();
                    //$cr->grid->js()->reload()->execute();
                }
                if($_GET['activate']){
                    $mq=$this->add('Model_Quote')->notDeleted()->getThisOrganisation()->load($_GET['activate']);
                    $mq->activate();
                    $mq->save();

                    $this->js()->univ()->redirect('quotes',array('active_tab'=>$this->active_tab))->execute();
                    //$cr->grid->js()->reload()->execute();
                }
            }

//            $cr->grid->addClass('zebra bordered');
//            $cr->grid->add('View_ExtendedPaginator',
//                array(
//                    'values'=>array('10','50','100'),
//                    'grid'=>$cr->grid,
//                ),
//                'extended_paginator');
            $cr->grid->addQuickSearch(array('quote.name','project'));
        }

    }
    function defaultTemplate() {
        $tab=$this->app->recall('active_tab');
//        $this->app->forget('active_tab');
        return array('page/quotes/base'.$tab);
    }
}
