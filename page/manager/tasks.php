<?php

class page_manager_tasks extends page_tasksfunctions {

    function initMainPage() {
    	$s=$this->add('View_Switcher');
    	
    	$this->add('View_TasksCRUD',array(
    			'newtask_link'=>'manager/tasks/new',
    			'allow_add'=>false,'allow_edit'=>true,'allow_del'=>true,
    			'edit_fields'=>array('name','descr_original','priority','status','estimate','requester_id','assigned_id'),
    			'show_fields'=>array('name','descr_original','priority','status','estimate','spent_time','requester','assigned'),
    			));
    }

}
