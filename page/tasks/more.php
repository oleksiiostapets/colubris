<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 10/1/13
 * Time: 11:15 PM
 * To change this template use File | Settings | File Templates.
 */
class page_tasks_more extends Page {
    function page_index(){
        if (!$_GET['task_id']) {
            throw $this->exception('task_id must be provided!');
        }
        $this->api->stickyGET('task_id');
        $task=$this->add('Model_Task')->load($_GET['task_id']);


        $v = $this->add('View');

        // Description
        $descr_view = $v->add('View')->addClass('span12');
        $descr_view->add('H4')->set('Description');
        $descr_view->add('View')->setHtml( $this->api->colubris->makeUrls(nl2br($task->get('descr_original'))) );

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
//    			array()
        );
*/

        $comments_view = $v->add('View');
        $comments_view->add('H4')->set('Comments');

        $cr=$comments_view->add('CRUD', array('grid_class'=>'Grid_Reqcomments'));

        $m=$comments_view->add('Model_Taskcomment')
            ->addCondition('task_id',$_GET['task_id']);

        $cr->setModel($m,
            array('text','file_id'),
            array('text','user','file','file_thumb','created_dts')
//    			array()
        );
        if($cr->grid){
            $cr->grid->addClass('zebra bordered');
            $cr->add_button->setLabel('Add Comment');
        }
        if($_GET['delete']){
            $comment=$this->add('Model_Taskcomment')->load($_GET['delete']);
            $comment->delete();
            $cr->js()->reload()->execute();
        }
    }
}