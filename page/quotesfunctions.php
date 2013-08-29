<?php

class page_quotesfunctions extends Page {
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


    function addBreacrumb($view,$quotes_link){
        $view->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Quotes',
                    'url' => $quotes_link,
                ),
                2 => array(
                    'name' => 'Details of Quotation (requirements)',
                    'url' => '',
                ),
            )
    	));
    }


    function addRequestForEstimateButton($view, $requirements, $quote) {
        if ($quote->hasUserRequestForEstimateAccess($this->api->auth->model)) {

            if($_GET['action']=='estimation'){
            	$quote->set('status','estimate_needed');
            	$quote->save();
            	$this->api->redirect($this->api->url('/manager/quotes'));
            }

   	        $but=$view->add('Button')->setClass('right')->set('Request for estimate','estimation');
            if ( count($requirements->getRows()) <= 0 ) $but->js(true)->attr('disabled','disabled');
            $but->js('click', array(
   	            $this->js()->univ()->redirect($this->api->url(null,array('quote_id'=>$_GET['quote_id'],'action'=>'estimation')))
   	        ));
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
        if($page[count($page)-1]!='step2'){
        	if( !($this->api->auth->model['is_developer']) &&
                ($quote->get('status')=='quotation_requested'
        			|| ( $this->api->auth->model['is_client'] && $quote->get('status')=='not_estimated' ))
            ){
		        $b=$v->add('Button')->set('Edit requirements');
		        $b->js('click')->univ()->redirect($this->api->url('/'.$page[0].'/quotes/rfq/step2',array('quote_id'=>$quote->get('id'))));
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
        $gr->setSource($source);

    }

    public $total_view;
    function addFloatingTotal($view, $quote) {
        $v = $view->add('View')->setClass('floating_total radius_10');
        $v->js(true)->colubris()->floating_total($v->name);
        $quote['estimated']>0?$estimate=$quote['estimated']:$estimate=0;
        $this->total_view = $v->add('View')
                ->setClass('estimate_total_time_to_reload')
                ->set('Estimated: '.$estimate.'hours');
        $this->total_view->js('reload')->reload();
        return $this->total_view;
    }

    function addEditRequirementButton($view, $quote, $role) {
        if ($quote->hasUserEditRequirementsAccess($this->api->auth->model)) {
            $b=$view->add('Button')->set('Edit requirements');
            $b->js('click')->univ()->redirect(
                $this->api->url('/'.$role.'/quotes/rfq/step2',array('quote_id'=>$this->quote->get('id')))
            );
        }
    }

    public $allow_included = true;
    function addRFQRequirements(
        $view, $quote, $requirements, $rights, $total_view, $edit_fields=array('name','descr','estimate','file_id')
    ) {
        $cr = $view->add('CRUD',
            array(
                'allow_add'=>$rights['allow_add'],'allow_edit'=>$rights['allow_edit'],'allow_del'=>$rights['allow_del'],
                'grid_class'=>'Grid_Requirements','quote'=>$quote,'total_view'=>$total_view,
                'allow_included'=>$quote->hasUserIsIncludedAccess($this->api->auth->model)
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
    }
        
}
