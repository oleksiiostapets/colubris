<?php

class page_client_tasks extends page_tasksfunctions {

    function initMainPage() {
    	$s=$this->add('View_Switcher');
    	
    	$this->add('View_TasksCRUD',array(
    			'newtask_link'=>'client/tasks/new',
    			'allow_add'=>false,'allow_edit'=>true,'allow_del'=>true,
    			'edit_fields'=>array('name','descr_original','priority','status','requester_id','assigned_id'),
    			'show_fields'=>array('name','priority','status','estimate','spent_time','requester','assigned'),
    			));    	
    }
        
}
