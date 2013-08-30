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
        $quote = $this->add('Model_Quote')->load($_GET['quote_id']); // TODO nice UI to show user if there is no quote

        // Does Project of this quotetion exist?
        $project=$this->add('Model_Project')->tryLoad($quote->get('project_id'));
        if( !$project->loaded() ){
        	$this->api->redirect('/denied');
        	// TODO nice UI to explain user that there is no such a project
            // TODO just redirect to denied is not clear
        }

        $requirements=$this->add('Model_Requirement');
        $requirements->addCondition('quote_id',$_GET['quote_id']);

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$quote->canUserReadRequirements($this->api->currentUser()) ){
            $this->api->redirect('/denied');
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

        // auote info grid
        $left->add('H4')->set('Quote:');
        $fields_required = array('project','user','name',/*'estimated',*/'general',);
        $this->addQuoteInfoGrid($left, $fields_required,$quote);

        // requirements
        $left->add('H4')->set('Requirements:');
        $left->add('View_Info')->set('Requirements, which will be added in the future increase estimation.');


        // | *** RIGHT *** |
        $right = $this->add('View')
                ->setClass('right span6')
                ->addStyle('margin-top','20px')
                ->addStyle('margin-bottom','20px')
        ;
        $this->addRequestForEstimateButton($right, $requirements, $quote);
        $this->addEstimationButtons($right, $quote);
        $total_view = $this->addFloatingTotal($right,$quote);


        $this->add('View')->setClass('clear');

        // grid with requirements
        $cr = $this->addRequirementsCRUD($this, $quote, $requirements, $this->requirements_rights, $total_view, $this->edit_fields);
        $this->addRequirementForm($this, $quote, $cr);

    }
    function page_more(){
        if (!isset($_GET['requirement_id'])) {
            throw $this->exception('Provide $_GET[\'requirement_id\']');
        }
    	$this->api->stickyGET('requirement_id');
    	$req=$this->add('Model_Requirement')->load($_GET['requirement_id']);

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
    		$cr->add_button->setLabel('Add Comment');
    		$cr->grid->setFormatter('text','text');
    	}
    	if($_GET['delete']){
    		$comment=$this->add('Model_Reqcomment')->load($_GET['delete']);
    		$comment->delete();
    		$cr->js()->reload()->execute();
    	}
    }





    function addBreacrumb($view){
        $view->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Quotes',
                    'url' => $this->role.'/quotes',
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
            	$this->api->redirect($this->api->url('/manager/quotes'));
            }

   	        $but=$view->add('Button')->setClass('right')->set('Request for estimate','estimation');
            if ( count($requirements->getRows()) <= 0 ) $but->js(true)->attr('disabled','disabled');
            $but->js('click', array(
   	            $this->js()->univ()->redirect($this->api->url(null,array('quote_id'=>$quote['id'],'action'=>'estimation')))
   	        ));
        }
    }

    function addEstimationButtons($view, $quote) {
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
            	$quote->set('status','estimated');
            	$quote->save();
                $this->js(null,$this->js()->reload())->execute();
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
        $fields_required = array('project','user','name',/*'estimated',*/'general',);
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
        $gr = $v->add('Grid');
        $gr->addColumn('text','name','');
        $gr->addColumn('text','value','Info');
        $gr->addFormatter('value','wrap');
        $gr->setSource($source);

    }

    public $total_view;
    function addFloatingTotal($view, $quote) {
        $v = $view->add('View')->setClass('floating_total radius_10');
        $v->js(true)->colubris()->floating_total($v->name);
        $quote['estimated']>0?$estimate=$quote['estimated']:$estimate=0;
        $total_view = $v->add('View')
                ->setClass('estimate_total_time_to_reload')
                ->set('Estimated: '.$estimate.'hours');
        $total_view->js('reload')->reload();
        return $total_view;
    }

    function addEditRequirementButton($view, $quote, $role) {
        if ($quote->canUserEditRequirements($this->api->currentUser())) {
            $b=$view->add('Button')->set('Edit requirements');
            $b->js('click')->univ()->redirect(
                $this->api->url('/'.$role.'/quotes/rfq/requirements',array('quote_id'=>$this->quote->get('id')))
            );
        }
    }

    public $allow_included = true;
    public $edit_fields = array('name','descr','estimate','file_id');
    function addRequirementsCRUD($view, $quote, $requirements, $rights, $total_view, $edit_fields) {
        $cr = $view->add('CRUD',
            array(
                'allow_add'=>$rights['allow_add'],'allow_edit'=>$rights['allow_edit'],'allow_del'=>$rights['allow_del'],
                'grid_class'=>'Grid_Requirements','quote'=>$quote,'total_view'=>$total_view,
            )
        );

        $cr->setModel($requirements,
                $edit_fields,
        		array('is_included','name','estimate','spent_time','file','user','count_comments')
         );

         if($cr->grid){
         	$cr->grid->addColumn('expander','more');
         	//$cr->grid->addFormatter('file','download');
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

}