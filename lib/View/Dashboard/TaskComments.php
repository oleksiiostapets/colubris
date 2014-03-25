<?php
/**
 * Created by Vadym Radvansky
 * Date: 3/25/14 2:52 PM
 */
class View_Dashboard_TaskComments extends View_Dashboard_Comments {
    protected $type = 'Task';
    function init() {
        parent::init();
        $this->template->del('task_del');
    }
    function formatRow() {
        parent::formatRow();

        // task
        if ($this->current_row['task_name']) {
            $this->current_row_html['task'] =
                    '<a href="'.$this->api->url('/task',array('task_id'=>$this->current_row['task_id'])).'">'
                            .$this->current_row['task_name'].'</a>';
        } else {
            $this->current_row_html['task'] = 'none';
        }
    }
}