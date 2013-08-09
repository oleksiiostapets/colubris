<?php

class page_team_tasks_new extends Page {

    function initMainPage() {
    	$s=$this->add('View_Switcher');
    	
    	$this->add('View_TasksNew',array('redirect_to'=>'/team/tasks','fields'=>array('name','descr_original','estimate','priority','type','status','requester_id','assigned_id')));

    }
}
