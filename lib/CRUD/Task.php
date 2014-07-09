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
        $this->addCreateButton();
    }
    function configure() {
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
            $g->add('Paginator')->ipp($this->items_per_page);
            //$g->js('reload')->reload();

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
    protected function addMoreFrame() {
        if($p = $this->addFrame('Info')){
            if (!$this->id) {
                throw $this->exception('task_id must be provided!');
            }
            $task = $this->add('Model_Task')->load($this->id);

            $v = $p->add('View');

            // Description
            $descr_view = $v->add('View')->addClass('span12');
            $descr_view->add('H4')->set('Description');
            $descr_view->add('View')->setHtml( $this->app->colubris->makeUrls($task->get('descr_original')) );

	        if (!$this->api->currentUser()->isClient()){
		        $time_view = $v->add('View');
		        $time_view->add('H4')->set('Spent Time');
		        $time_view->add('CRUD_TaskTime',array('task_id'=>$this->id));
	        }

	        $comments_view = $v->add('View');
            $comments_view->add('H4')->set('Comments');
	        $comments_view->add('CRUD_TaskComments',array('task_id'=>$this->id));

        }
    }
    protected function addCreateButton() {
        $b = $this->addButton('Create New Task');
        $b->js('click')->redirect('task');
    }
}