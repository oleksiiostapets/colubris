<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 10/1/13
 * Time: 11:14 PM
 * To change this template use File | Settings | File Templates.
 */
class page_tasks_time extends Page {
    function page_index(){

        if (!$_GET['task_id']) {
            throw $this->exception('task_id must be provided!');
        }

        $this->api->stickyGet('task_id');
        $model=$this->add('Model_TaskTime')->addCondition('task_id',$_GET['task_id']);
        $crud=$this->add('CRUD');
        if ($this->app->currentUser()->get('is_client')){
            $crud->setModel($model,
                array('spent_time','comment','date'),
                array('user','spent_time','comment','date','remove_billing')
            );
        }else{
            $crud->setModel($model,
                array('spent_time','comment','date','remove_billing'),
                array('user','spent_time','comment','date','remove_billing')
            );
        }
        if ($crud->grid){
            $crud->grid->addClass('zebra bordered');
            $crud->add_button->setLabel('Add Time');
        }

        if ($_GET['reload_view']) {
            $this->js(true)->closest(".ui-dialog")->on("dialogbeforeclose",
                $this->js(null,'function(event, ui){
                            '.$this->js()->_selector('#'.$_GET['reload_view'])->trigger('reload').'
                        }
                ')
            );
        }

    }
}