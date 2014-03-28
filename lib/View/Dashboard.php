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

    /* ************************************************************************************
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

    /* ************************************************************************************
     *  Block with comments on quotes requirements which user have access to
     */
    protected function addQuoteComments() {
        $this->add('View_Dashboard_QuoteComments',null,'crud_com_quote');
    }

    /* ************************************************************************************
     *  Block with comments on requirements tasks which user have access to
     */
    protected function addTaskComments() {
        $this->add('View_Dashboard_TaskComments',null,'crud_comm_tasks');
    }

    /* ************************************************************************************
     *  Block with tasks which user have access to
     */
    protected function addTasks() {
        $cr = $this->add('CRUD',array(
            'grid_class'=>'Grid_Tasks',
            'allow_add'=>$this->allow_add,
            'allow_edit'=>$this->allow_edit,
            'allow_del'=>$this->allow_del
        ),'crud_active_tasks');

        $cr->setModel(
            $this->add('Model_Task')->addDashCondition(),
            $this->edit_fields,
            $this->show_fields
        );

        $this->addTimeFrame($cr);
        $this->addMoreFrame($cr);


        if($cr->grid){
//            $cr->grid->addColumn('button','attachments');
//            if ($_GET['attachments']) {
//                $this->js()->univ()->frameURL($this->app->_('Attachments'),array(
//                    $this->app->url('./attachments',array('task_id'=>$_GET['attachments'],'reload_view'=>$cr->grid->name))
//                ))->execute();
//            }
            $cr->grid->addPaginator(10);
        }
    }
    private function addTimeFrame(CRUD $cr) {
        if(!$this->app->auth->model->isClient()){
            if($p = $cr->addFrame('Time')){

                if (!$cr->id) {
                    throw $this->exception('task_id must be provided!');
                }

                //$this->api->stickyGet('task_id');
                $model = $p->add('Model_TaskTime')->addCondition('task_id',$cr->id);
                $crud = $p->add('CRUD');
                  if ($p->app->currentUser()->isClient()){
                      $crud->setModel($model,
                          array('spent_time','comment','date'),
                          array('user','estimate','comment','date','remove_billing')
                      );
                  } else {
                      $crud->setModel($model,
                          array('spent_time','comment','date','remove_billing'),
                          array('user','spent_time','comment','date','remove_billing')
                      );
                  }
                if ($crud->grid){
                    $crud->grid->addClass('zebra bordered');
                }
                if ($crud->add_button) {
                    $crud->add_button->setLabel('Add Time');
                }

                $p->js(true)->closest(".ui-dialog")->on("dialogbeforeclose",
                    $p->js(null,'function(event, ui){
                              '.$p->js()->_selector('#'.$cr->name)->trigger('reload').'
                          }
                      ')
                );

            }
        }
    }
    private function addMoreFrame(CRUD $cr) {
        if($p = $cr->addFrame('More')){
            if (!$cr->id) {
                throw $this->exception('task_id must be provided!');
            }
            $task = $this->add('Model_Task')->load($cr->id);

            $v = $p->add('View');

            // Description
            $descr_view = $v->add('View')->addClass('span12');
            $descr_view->add('H4')->set('Description');
            $descr_view->add('View')->setHtml( $this->app->colubris->makeUrls($task->get('descr_original')) );

                  /*
                  // left view
                  $left_view = $v->add('View')->setClass('span6 right');
                  $left_view->add('H4')->set('Attachments');

                    $model=$left_view->add('Model_Attach')->addCondition('task_id',$_GET['task_id']);
                    $crud=$left_view->add('CRUD',array(
                         'grid_class' => 'Grid_Attachments',
                     ));
                    $crud->setModel($model,
                            array('description','file_id'),
                            array('description','file','file_thumb','updated_dts')
                    );
                    */

            $comments_view = $v->add('View');
            $comments_view->add('H4')->set('Comments');

            $crud = $comments_view->add('CRUD', array('grid_class'=>'Grid_Reqcomments'));

            $m = $comments_view->add('Model_Taskcomment')
                    ->addCondition('task_id',$cr->id);
            $crud->setModel($m,
                array('text','file_id'),
                array('text','user','user_id','file','file_thumb','created_dts')
            );
            if($crud->grid){
                $crud->grid->addClass('zebra bordered');
            }
            if ($crud->add_button) {
                $crud->add_button->setLabel('Add Comment');
            }
            if($_GET['delete']){
                $comment=$this->add('Model_Taskcomment')->load($_GET['delete']);
                $comment->delete();
                $crud->js()->reload()->execute();
            }

        }
    }
}
