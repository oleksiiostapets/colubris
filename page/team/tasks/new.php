<?php

class page_team_tasks_new extends Page {

    function page_index() {
        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Tasks',
                    'url' => 'team/tasks',
                ),
                2 => array(
                    'name' => 'new',
                ),
            )
        ));

        $s=$this->add('View_Switcher');
    	
    	$this->add('View_TasksNew',array('redirect_to'=>'/team/tasks','fields'=>array('name','descr_original','estimate','priority','type','status','requester_id','assigned_id')));

    }
}
