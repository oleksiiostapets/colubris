<?php
class View_Dashboard extends View {
    public $allow_add  = false;
    public $allow_edit = false;
    public $allow_del  = false;
    function init(){
        parent::init();

        if (
            $this->app->currentUser()->isAdmin() ||
            $this->app->currentUser()->isSystem()
        ) {
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
//    protected function addQuoteComments() {
//        $cr=$this->add('CRUD',
//            array('grid_class'=>'Grid_Dashcomments','allow_add'=>false,'allow_edit'=>false,'allow_del'=>false),
//            'crud_com_quote');
//
//        $m = $this->app->currentUser()->getDashboardCommentsToReqModel();
//
//        $cr->setModel($m,
//            array('text','file_id'),
//            array('text','user','file','file_thumb','created_dts','project_name','quote_name','quote_status','requirement_name','quote_id','requirement_id')
//        );
//
//        if ($cr->grid){
//            $cr->grid->addPaginator(5);
//            $cr->grid->addFormatter('project_name','wrap');
//
//            $cr->grid->addColumn('button','check_reqcomment','√');
//            if($_GET['check_reqcomment']){
//                $comment_user=$this->add('Model_ReqcommentUser');
//                $comment_user->set('reqcomment_id',$_GET['check_reqcomment']);
//                $comment_user->set('user_id',$this->app->auth->model['id']);
//                $comment_user->save();
//
//                $cr->grid->js()->reload()->execute();
//            }
//        }
//    }

    /* ****************************
     *  Block with comments on requirements tasks which user have access to
     */
    protected function addTaskComments() {
        $cr=$this->add('CRUD',array('grid_class'=>'Grid_Dashcomments','allow_add'=>false,'allow_edit'=>false,'allow_del'=>false),'crud_comm_tasks');

        if ($this->app->currentUser()->isClient()) $m=$this->add('Model_Taskcomment_Client');
        elseif ($this->app->currentUser()->isDeveloper()) $m=$this->add('Model_Taskcomment_Developer');
        else $m=$this->add('Model_Taskcomment');
        $m->addCondition('user_id','<>',$this->app->auth->model['id']);
        $m->setOrder('created_dts',true);

        $proxy_check=$this->add('Model_TaskcommentUser');
        $proxy_check->addCondition('user_id',$this->app->auth->model['id']);
        $proxy_check->_dsql()->field('taskcomment_id');
        $m->addCondition('id','NOT IN',$proxy_check->_dsql());

        $jt = $m->join('task.id','task_id','left','_t');
        $jt->addField('task_name','name');

        $jp = $jt->join('project.id','project_id','left','_pr');
        $jp->addField('project_name','name');
        $jp->addField('organisation_id','organisation_id');
        $m->addCondition('organisation_id',$this->app->auth->model['organisation_id']);

        $cr->setModel($m,
            array('text','file_id'),
            array('text','user','file','file_thumb','created_dts','project_name','task_name','task_id')
        );

        if ($cr->grid){
            $cr->grid->addPaginator(5);
            $cr->grid->addFormatter('project_name','wrap');
            //$cr->grid->addFormatter('task_name','wrap');

            $cr->grid->addColumn('button','check_taskcomment','√');
            if($_GET['check_taskcomment']){
                $comment_user=$this->add('Model_TaskcommentUser');
                $comment_user->set('taskcomment_id',$_GET['check_taskcomment']);
                $comment_user->set('user_id',$this->app->auth->model['id']);
                $comment_user->save();

                $cr->grid->js()->reload()->execute();
            }
        }
    }

    /* ****************************
     *  Block with tasks which user have access to
     */
    protected function addTasks() {
        $cr=$this->add('CRUD',array('grid_class'=>'Grid_Tasks','allow_add'=>$this->allow_add,'allow_edit'=>$this->allow_edit,'allow_del'=>$this->allow_del),'crud_active_tasks');
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

        	$cr->grid->addFormatter('status','status');
            $cr->grid->addPaginator(10);

        }
    }
}
