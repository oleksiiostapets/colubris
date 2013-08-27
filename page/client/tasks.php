<?php

class page_client_tasks extends page_tasksfunctions {

        function page_index() {

        $this->add('View')->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Tasks',
                    'url' => 'manager/tasks',
                ),
            )
        ));

    	$s=$this->add('View_Switcher');
    	
    	$this->add('View_TasksCRUD',array(
    			'newtask_link'=>'client/tasks/new',
    			'allow_add'=>false,'allow_edit'=>true,'allow_del'=>true,
    			'edit_fields'=>array('name','descr_original','priority','type','status','requester_id','assigned_id'),
    			'show_fields'=>array('name','priority','type','status','estimate','spent_time','requester','assigned'),
        ));
    }
        
}
