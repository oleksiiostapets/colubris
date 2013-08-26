<?php

class page_manager_dashboard extends page_dashboard {

    function init() {
        parent::init();

        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Dashboard',
                    'url' => 'manager/dashboard',
                ),
            )
        ));
    }

    function page_index() {
    	$this->add('View_Dashboard',array(
    			'allow_add'=>false,'allow_edit'=>true,'allow_del'=>true,
    			'edit_fields'=>array('project_id','name','descr_original','priority','status','estimate','requester_id','assigned_id'),
    			'show_fields'=>array('project','name','priority','status','estimate','spent_time','requester','assigned','updated_dts'),
        ));
    }

}
