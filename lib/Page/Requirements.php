<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 8/28/13
 * Time: 11:53 PM
 * To change this template use File | Settings | File Templates.
 */
class Page_Requirements extends Page {

    function page_index(){


        /* ***************************
         *
         *         PREPARATION
         *
         */
        if (!isset($_GET['quote_id'])) throw $this->exception('Provide $_GET[\'quote_id\']');
    	$this->api->stickyGet('quote_id');
    	$this->api->stickyGet('project_id');
        $quote = $this->add('Model_Quote')->load($_GET['quote_id']); // TODO nice UI to show user if there is no quote

        // Does Project of this quotetion exist?
        $project=$this->add('Model_Project')->tryLoad($quote->get('project_id'));
        if( !$project->loaded() ){
            throw $this->exception('There is no such a project','Exception_Denied');
        	// TODO nice UI to explain user that there is no such a project
            // TODO just redirect to denied is not clear
        }

        // Storing project id for assigned and requester
        $this->api->memorize('project_id',$project->get('id'));

        $requirements=$this->add('Model_Requirement');
        $requirements->addCondition('quote_id',$_GET['quote_id']);

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$quote->canUserReadRequirements($this->api->currentUser()) ){
            throw $this->exception('You cannot see requirements of this quote','Exception_Denied');
        }




        /* ***************************
         *
         *          HTML
         *
         */
        $this->addBreacrumb($this);

        // | *** LEFT *** |
        $left = $this->add('View')
                ->setClass('left span6')
                ->addStyle('margin-top','20px')
                ->addStyle('margin-bottom','20px')
        ;
        $left->add('H1')->set('Requirements for Quotation');

        // quote info grid
        $left->add('H4')->set('Quote:');
        $fields_required = array('project','user','name',/*'estimated',*/'general_description',);
        $this->addQuoteInfoGrid($left, $fields_required,$quote);

        // | *** RIGHT *** |
        $right = $this->add('View')
                ->setClass('right span6')
                ->addStyle('margin-top','20px')
                ->addStyle('margin-bottom','20px')
        ;
        $this->addRequestForEstimateButton($right, $requirements, $quote);
        $this->addApproveButton($right, $quote);
        $this->addEstimationButtons($right, $quote, $requirements);
        $total_view = $this->addFloatingTotal($right,$quote);

        $this->add('View')->setClass('clear');

        // | *** LEFT *** |
        $left = $this->add('View')
            ->setClass('left span6')
            ->addStyle('margin-top','20px')
            ->addStyle('margin-bottom','20px');

        // requirements
        $left->add('H4')->set('Requirements:');
        $left->add('View_Info')->set('Requirements, which will be added in the future increase estimation.');

        // | *** RIGHT *** |
        $right = $this->add('View')
            ->setClass('right span6')
            ->addStyle('margin-top','20px')
            ->addStyle('margin-bottom','20px')
        ;

        if (!$this->api->currentUser()->isClient()) $this->addProgressBars($right,$quote);

        $this->add('View')->setClass('clear');

        // grid with requirements
        $cr = $this->addRequirementsCRUD($this, $quote, $requirements, $total_view);
        $this->addRequirementForm($this, $quote, $cr);

    }
    function page_more(){
        $this->api->stickyGET('requirement_id');

    	$req=$this->add('Model_Requirement')->load($_GET['requirement_id']);
        $quote=$this->add('Model_Quote')->load($req->get('quote_id'));
        $_GET['project_id']=$quote->get('project_id');

    	$this->add('View')->setHtml('<strong>Description:</strong> '.$this->api->colubris->makeUrls($req->get('descr')));

    	$this->add('View')->setHtml('<hr /><strong>Comments:</strong> ');

    	$cr=$this->add('CRUD', array('grid_class'=>'Grid_Reqcomments'));

    	$m=$this->add('Model_Reqcomment')
    			->addCondition('requirement_id',$_GET['requirement_id']);
    	$cr->setModel($m,
    			array('text','file_id'),
    			array('text','user','file','file_thumb','created_dts')
    	);
    	if($cr->grid){
            $cr->grid->addClass('zebra bordered');
    		$cr->add_button->setLabel('Add Comment');
    		//$cr->grid->setFormatter('text','text');
        }

        $this->tasks=$this->add('Model_Task_RestrictedUsers');
        $this->tasks->addCondition('project_id',$req['project_id']);
        $this->tasks->addCondition('requirement_id',$_GET['requirement_id']);
        $this->addTasksCRUD($this);

    }





    function addBreacrumb($view){
        $view->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Quotes',
                    'url' => 'quotes',
                ),
                2 => array(
                    'name' => 'Details of Quotation (requirements)',
                    'url' => '',
                ),
            )
    	));
    }

    function addRequestForEstimateButton($view, $requirements, $quote) {
        if ($quote->canUserRequestForEstimate($this->api->currentUser())) {

            if($_GET['action']=='estimation'){
            	$quote->set('status','estimate_needed');
            	$quote->save();
            	$this->api->redirect($this->api->url('/quotes'));
            }

   	        $but=$view->add('Button')->setClass('right')->set('Submit for Quotation','estimation');
            if ( count($requirements->getRows()) <= 0 ) $but->js(true)->attr('disabled','disabled');
            $but->js('click', array(
   	            $this->js()->univ()->redirect($this->api->url(null,array('quote_id'=>$quote['id'],'action'=>'estimation')))
   	        ));
        }
    }

    function addApproveButton($view, $quote) {
        if ($quote->canUserApproveQuote($this->api->currentUser())) {

            if($_GET['action']=='estimation_approved'){
                $quote->set('status','estimation_approved');
                $quote->save();

                $this->api->mailer->addClientReceiver($quote->get('project_id'));
                $this->api->mailer->addAllManagersReceivers($this->api->auth->model['organisation_id']);
                $this->api->mailer->sendMail('quote_approved',array(
                    'quotename'=>$quote->get('name'),
                    'link'=>$this->api->url('quotes/rfq/requirements',array('quote_id'=>$quote->get('id'))),
                ));

                $this->api->redirect($this->api->url('/quotes'));
            }

            $but=$view->add('Button')->setClass('right')->set('Approve Quotation','estimation_approved');
            $but->js('click', array(
                $this->js()->univ()->redirect($this->api->url(null,array('quote_id'=>$quote['id'],'action'=>'estimation_approved')))
            ));
        }
    }

    function addEstimationButtons($view, $quote, $requirements) {
        if ($quote->canUserEstimateQuote($this->api->currentUser())) {

            $bs = $view->add('ButtonSet')->addClass('right');
            $bs->add('Button')
                ->addClass('green-button')
                ->set('Estimation finished','estimation_finished')
                ->js('click')
                ->univ()->ajaxec($this->api->url(null,array('quote_id'=>$quote['id'],'action'=>'estimated')))
            ;
            $bs->add('Button')
                ->addClass('red-button')
                ->set('Cannot estimate','cannot_estimate')
                ->js('click')
                ->univ()->ajaxec($this->api->url(null,array('quote_id'=>$quote['id'],'action'=>'not_estimated')))
            ;

            // actions
            if($_GET['action']=='estimated'){
                if ($quote->canStatusBeChangedToEstimated($requirements)){
                    $quote->set('status','estimated');
                    $quote->save();
                    $this->js(null,$this->js()->reload())->execute();
                }else{
                    $this->js()->univ()->errorMessage('All requirements need estimations!')->execute();
                }
            }

            if($_GET['action']=='not_estimated'){
            	$quote->set('status','not_estimated');
            	$quote->save();
                $this->js(null,$this->js()->reload())->execute();
            }
        }
    }

    function addRFQQuote($view,$quote) {
        $v=$view->add('View');//->setClass('left span6');

        // quote description
        $v->add('H4')->set('Quote:');
        $fields_required = array('project','user','name',/*'estimated',*/'general_description',);
        $this->addQuoteInfoGrid($v, $fields_required,$quote);

        //$v=$this->add('View')->setClass('right');
        $page=explode('_',$this->api->page);
        if($page[count($page)-1]!='requirements'){
        	if( !($this->api->auth->model['is_developer']) &&
                ($quote->get('status')=='quotation_requested'
        			|| ( $this->api->auth->model['is_client'] && $quote->get('status')=='not_estimated' ))
            ){
		        $b=$v->add('Button')->set('Edit requirements');
		        $b->js('click')->univ()->redirect($this->api->url('/'.$page[0].'/quotes/rfq/requirements',array('quote_id'=>$quote->get('id'))));
            }
        }

        $v=$this->add('View')->setClass('clear');

        $v=$this->add('View')->setClass('left');
        $v->add('H4')->set('Requirements:');

        $v=$this->add('View')->setClass('floating_total radius_10');
        $v->js(true)->colubris()->floating_total($v->name);
        $quote->get('estimated')>0?$estimate=$quote->get('estimated'):$estimate=0;
        $this->total_view = $v->add('View')
                ->setClass('estimate_total_time_to_reload')
                ->set('Estimated: '.$estimate.'hours');
        $this->total_view->js('reload')->reload();

        $v=$this->add('View')->setClass('clear');

    }

    function addQuoteInfoGrid($v,$fields_required,$quote) {
        $count = 0;
        $source = array();
        foreach ($quote->get() as $key=>$value) {
            if (in_array($key,$fields_required)) {
                $source[$count]['name'] = ucwords($key);
                $source[$count]['value'] = $value;
                $count++;
            }
        }
        $gr = $v->add('Grid_Quote');
        $gr->addColumn('text','name','');
        $gr->addColumn('text','value','Info');
        $gr->addFormatter('value','wrap');
        $gr->setSource($source);

    }

    public $total_view;
    function addFloatingTotal($view, $quote) {
        $v = $view->add('View')->setClass('floating_total radius_10');
        $v->js(true)->colubris()->floating_total($v->name);

        $total_view = $v->add('View')->setClass('estimate_total_time_to_reload');

        if (count($fields = $this->api->currentUser()->getFloatingTotalFields())) {
            $total_view->add('View')->set('Estimated: ');
            foreach ($fields as $field) {
                switch ($field) {
                    case 'estimpay';
                        $total_view->add('View')->addClass('estimated-cost')
                            ->set('Cost: '.(($quote['estimpay']=='')?'-':$quote['estimpay'].' '.$quote['currency']));
                        break;
                    case 'estimated';
                        $total_view->add('View')->addClass('estimated-time')
                            ->set('Time: '.(($quote['estimated']>0)?$quote['estimated']:'0').' hours');
                        break;
                }
            }
            $total_view->js('reload')->reload();
        }

        return $total_view;
    }
    function addProgressBars($view, $quote) {
        $v = $view->add('View')->setClass('floating_total radius_10 progress_bars');
        $v->add('View')->set('Progress:');
        if ($quote->get('estimated')>0){
            $progress=floor((100*$quote->get('spent_time'))/$quote->get('estimated'));
        }else{
            $progress=1;
        }
        if($progress==0) $progress=1;
        $class='success';
        if ($progress>100){
            $progress = 100;
            $class='error';
        }
        if ($quote->get('spent_time')>0) $spent=$quote->get('spent_time').'hours'; else $spent='';
        if ($this->api->currentUser()->isClient()) $spent='';
        $progress_view = $v->add('View')->setHtml('
<div class="ui-progress-bar '.$class.' ui-container" id="progress_bar">
    <div class="ui-progress" style="width: '.$progress.'%;">
        '.$spent.'
              <span class="ui-label" style="display:none;">
                Loading Resources
                <b class="value">'.$progress.'%</b>
              </span>
    </div>
</div>
');

        return $progress_view;
    }

    function addEditRequirementButton($view, $quote) {
        if ($quote->canUserEditRequirements($this->api->currentUser())) {
            $b=$view->add('Button')->set('Edit requirements');
            $b->js('click')->univ()->redirect(
                $this->api->url('quotes/rfq/requirements',array('quote_id'=>$this->quote->get('id')))
            );
        }
    }

    public $allow_included = true;
    public $edit_fields = array('name','descr','estimate','file_id');
    function addRequirementsCRUD($view, $quote, $requirements, $total_view) {
        $cr = $view->add('CRUD',
            array(
                'allow_add'    => false, // we cannot add from crud TODO make add from CRUD only
                'allow_edit'   => $quote->canUserEditRequirements($this->api->currentUser()),
                'allow_del'    => $quote->canUserDeleteRequirement($this->api->currentUser()),
                'grid_class'   => 'Grid_Requirements',
                'quote'        =>$quote,
                'total_view'   =>$total_view,
            )
        );

        if ( ($this->api->currentUser()->isClient()) || ($this->api->currentUser()->canSeeFinance()) ){
            $requirements->addExpression('cost')->set(function($m,$q)use($quote){
                if($quote['rate']!=''){
                    return "concat(round(estimate * ".$quote['rate']."),' ".$quote['currency']."' )";
                }
                else{
                    return 1;
                }
            });
        }
        $cr->setModel($requirements,
                $quote->whatRequirementFieldsUserCanEdit($this->api->currentUser()),
                $quote->whatRequirementFieldsUserCanSee($this->api->currentUser())
        );

        $cr->js('reload',$total_view->js()->trigger('reload'));

        if($cr->grid){
            $cr->grid->addClass('zebra bordered');
         	$cr->grid->addColumn('expander','more');
         	$cr->grid->addFormatter('file','download');
         	//$cr->grid->addFormatter('estimate','estimate');
         	$cr->grid->setFormatter('name','wrap');
        }
        return $cr;
    }

    function addRequirementForm($view, $quote, $crud) {
        // Checking client's edit permission to this quote and redirect to denied if required
        if( $quote->canUserEditRequirements($this->api->currentUser()) ){

            $view->add('H4')->set('New Requirement:');

            $form=$view->add('Form');
            $m=$this->setModel('Model_Requirement');
            $form->setModel($m,array('name','descr','file_id'));
            $form->addSubmit('Save');

            if($form->isSubmitted()){
            	$form->model->set('user_id',$this->api->auth->model['id']);
            	$form->model->set('quote_id',$quote['id']);
            	$form->update();

                $form->js(null,array(
                    $crud->js()->trigger('reload'),
                ))->reload()->execute();
            }
        }
    }

    function addTasksCRUD($view) {
        $user = $this->api->currentUser();
        $cr = $view->add('CRUD', array(
            'grid_class'      => 'Grid_Tasks',
            'allow_add'       => true,
            'allow_edit'      => true,
            'allow_del'       => true,
        ));

        $cr->setModel(
            $this->tasks,
            $this->tasks->whatTaskFieldsUserCanEdit($user),
            $this->tasks->whatTaskFieldsUserCanSee($user)
        );
    }

}