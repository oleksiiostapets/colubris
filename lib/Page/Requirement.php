<?php
class Page_Requirement extends Page {

    public $requirement;

    function page_index(){


        /* ***************************
         *
         *         PREPARATION
         *
         */
        if (!isset($_GET['requirement_id'])) throw $this->exception('Provide $_GET[\'requirement_id\']');
    	$this->api->stickyGet('requirement_id');
        $this->requirement = $this->add('Model_Requirement')->tryLoad($_GET['requirement_id']);
        if (!$this->requirement->loaded()) throw $this->exception('Requirement not found!');

        $quote = $this->add('Model_Quote')->load($this->requirement->get('quote_id'));

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
        $left->add('H1')->set($this->requirement->get('name'));

        // details
        $left->add('H4')->set('Details:');
        $this->addRequirementInfoGrid($left,$quote);

        $this->add('View')->setClass('clear');

        // grid with comments
        $cr = $this->addCommentsCRUD($this);
        // grid with tasks
        $this->tasks=$this->add('Model_Task_RestrictedUsers');
        $this->tasks->addCondition('project_id',$this->requirement->get('project_id'));
        $this->tasks->addCondition('requirement_id',$_GET['requirement_id']);
        $this->addTasksCRUD($this,$quote);
    }
    function page_more(){
        $this->api->stickyGET('requirement_id');

    	$req=$this->add('Model_Requirement')->load($_GET['requirement_id']);
        $quote=$this->add('Model_Quote')->load($req->get('quote_id'));
        $_GET['project_id']=$quote->get('project_id');

    	$this->add('View')->setHtml('<strong>Description:</strong> '.$this->api->colubris->makeUrls(nl2br($req->get('descr'))));

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
        $this->addTasksCRUD($this,$quote);

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
                    'name' => 'Quote',
                    'url' => $this->api->url('quotes/rfq/requirements',array('quote_id'=>$this->requirement->get('quote_id'))),
                ),
                3 => array(
                    'name' => 'Details of requirement',
                    'url' => '',
                ),
            )
    	));
    }

    function addRequirementInfoGrid($v,$quote) {
        $source = array(
            0=>array(
                'name'=>'Project Name',
                'value'=>$quote->get('project'),
            ),
            1=>array(
                'name'=>'Quote Name',
                'value'=>$quote->get('name'),
            ),
            2=>array(
                'name'=>'Description',
                'value'=>$this->requirement->get('descr'),
            )
        );
        $gr = $v->add('Grid_Quote');
        $gr->addColumn('text','name','');
        $gr->addColumn('text','value','Info');
        $gr->addFormatter('value','wrap');
        $gr->setSource($source);

    }

    function addCommentsCRUD($view) {
        $view->add('View')->setHtml('<strong>Comments:</strong>');
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
    }

    function addTasksCRUD($view,$quote) {
        $view->add('View')->setHtml('<strong>Tasks:</strong>');
        $user = $this->api->currentUser();
        if (!$quote->isExpired()){
            $allow_add=true;
            $can_edit=true;
            $can_del=true;
        }else{
            $allow_add=false;
            $can_edit=false;
            $can_del=false;
        }
        $cr = $view->add('CRUD', array(
            'grid_class'      => 'Grid_Tasks',
            'allow_add'       => $allow_add,
            'allow_edit'      => $can_edit,
            'allow_del'       => $can_del,
        ));

        $cr->setModel(
            $this->tasks,
            $this->tasks->whatTaskFieldsUserCanEdit($user),
            $this->tasks->whatTaskFieldsUserCanSee($user)
        );
    }

}