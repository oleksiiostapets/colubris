<?php
class View_Dashboard extends View {

    public $allow_add  = false;
    public $allow_edit = false;
    public $allow_del  = false;
    public $edit_fields;
    public $show_fields;

    function init(){
        parent::init();

        // admin and sys have no dash
        if ( $this->app->currentUser()->isAdmin() || $this->app->currentUser()->isSystem() ) {
            $this->template->del('delete_dash');
            return;
        }

        $this->addClientActionBlock();
        $this->addQuoteComments();
        $this->addTaskComments();
        $this->addTasks();
    }
    function defaultTemplate() {
        return array('view/dashboard');
    }

    /* ****************************
     *  Block with action for Client
     */
    protected function addClientActionBlock() {
        if ($this->app->currentUser()->isClient()) {
            $client_row = $this->add('View',null,'ClientActions')->addClass('atk-box ui-helper-clearfix');
            $client_row->add('H3')->setText('Actions:');
            $b = $client_row->add('Button')->set('Request For Quotation');
            $b->addStyle('margin-bottom','10px');
            $b->js('click', array(
                $this->js()->univ()->redirect($this->app->url('quotes/rfq'))
            ));

            $b = $client_row->add('Button')->set('Create Task');
            $b->addStyle('margin-bottom','10px');
            $b->js('click', array(
                $this->js()->univ()->redirect($this->app->url('tasks/new'))
            ));
        }
    }

    /* ****************************
     *  Block with comments on quotes requirements which user have access to
     */
    protected function addQuoteComments() {
        $this->add('View_Dashboard_QuoteComments',null,'crud_com_quote');
    }

    /* ****************************
     *  Block with comments on requirements tasks which user have access to
     */
    protected function addTaskComments() {
        $this->add('View_Dashboard_TaskComments',null,'crud_comm_tasks');
    }

    /* ****************************
     *  Block with tasks which user have access to
     */
    protected function addTasks() {
        $cr=$this->add('CRUD',array(
            'grid_class'=>'Grid_Tasks',
            'allow_add'=>$this->allow_add,
            'allow_edit'=>$this->allow_edit,
            'allow_del'=>$this->allow_del
        ),'crud_active_tasks');
        $m=$this->add('Model_Task');
        if (!$_GET['submit']) {
            $m->addCondition('status','<>','accepted');
        }
        $q=$m->_dsql();
        $q->where($q->orExpr()
        		->where('requester_id',$this->app->auth->model['id'])
        		->where('assigned_id',$this->app->auth->model['id'])
        );

        $cr->setModel($m,
            $this->edit_fields,
            $this->show_fields
        );

		if($cr->grid){
            $cr->grid->addFormatter('name','wrap');
        	$cr->grid->js('reload')->reload();

        	if(!$this->app->auth->model['is_client']){
   	        	$cr->grid->addColumn('button','time');
	            if ($_GET['time']) {
	                $this->js()->univ()->frameURL($this->app->_('Time'),array(
	                    $this->app->url('./time',array('task_id'=>$_GET['time'],'reload_view'=>$cr->grid->name))
	                ))->execute();
	            }
        	}
/*
            $cr->grid->addColumn('button','attachments');
            if ($_GET['attachments']) {
                $this->js()->univ()->frameURL($this->app->_('Attachments'),array(
                    $this->app->url('./attachments',array('task_id'=>$_GET['attachments'],'reload_view'=>$cr->grid->name))
                ))->execute();
            }
*/
            $cr->grid->addColumn('expander','more');

        	//$cr->grid->addFormatter('status','status');
            $cr->grid->addPaginator(10);

        }
    }
}
