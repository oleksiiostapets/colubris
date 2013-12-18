<?php

class page_tasks_new extends Page {

    function page_index() {
        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Tasks',
                    'url' => 'tasks',
                ),
                2 => array(
                    'name' => 'new',
                ),
            )
        ));

        $this->add('View_Switcher',array('class'=>'left'));
    	
    	$this->add('View_TasksNew',array('fields'=>array('name','descr_original','estimate','priority','type','status','requester_id','assigned_id')));

    }
}
