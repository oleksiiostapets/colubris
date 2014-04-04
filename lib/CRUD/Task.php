<?php
/**
 * Created by Vadym Radvansky
 * Date: 3/28/14 11:37 AM
 */
class CRUD_Task extends CRUD {

    public $form_class = 'Form_EditTask';
    public $grid_class = 'Grid_Tasks';
    public $items_per_page = 25;

    function init() {
        parent::init();
    }
    function configure() {
        $this->addTimeFrame();
        $this->addMoreFrame();
    }

    protected function configureGrid($fields) {
        parent::configureGrid($fields);
        if($g = $this->grid){
            $g->addClass('zebra bordered');
//            $g->add('View_ExtendedPaginator',
//                array(
//                    'values'=>array('10','50','100'),
//                    'grid'=>$g,
//                ),
//                'extended_paginator');
            $this->grid->addPaginator($this->items_per_page);
            $g->js('reload')->reload();

            $g->addQuickSearch(array('name'));

//            if(!$this->app->currentUser()->isClient()){
//                $g->addColumn('button','time');
//                if ($_GET['time']) {
//                    $this->js()->univ()->frameURL($this->api->_('Time'),array(
//                        $this->api->url('./time',array('task_id'=>$_GET['time'],'reload_view'=>$cr->grid->name))
//                    ))->execute();
//                }
//            }


//            $cr->grid->addColumn('button','attachments');
//            if ($_GET['attachments']) {
//                $this->js()->univ()->frameURL($this->app->_('Attachments'),array(
//                    $this->app->url('./attachments',array('task_id'=>$_GET['attachments'],'reload_view'=>$cr->grid->name))
//                ))->execute();
//            }
        }
    }
    protected function addTimeFrame() {
        if(!$this->app->auth->model->isClient()){
            if($p = $this->addFrame('Time')){

                if (!$this->id) {
                    throw $this->exception('task_id must be provided!');
                }

                //$this->api->stickyGet('task_id');
                $model = $p->add('Model_TaskTime')->addCondition('task_id',$this->id);
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
                              '.$p->js()->_selector('#'.$this->name)->trigger('reload').'
                          }
                      ')
                );

            }
        }
    }
    protected function addMoreFrame() {
        if($p = $this->addFrame('More')){
            if (!$this->id) {
                throw $this->exception('task_id must be provided!');
            }
            $task = $this->add('Model_Task')->load($this->id);

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
                    ->addCondition('task_id',$this->id);
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