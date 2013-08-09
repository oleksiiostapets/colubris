<?php

class page_client_dashboard extends page_dashboard {

    function initMainPage() {
    	$this->add('View_Dashboard',array(
    			'allow_add'=>false,'allow_edit'=>true,'allow_del'=>true,
    			'edit_fields'=>array('name','descr_original','priority','status','requester_id','assigned_id'),
    			'show_fields'=>array('project','name','priority','status','estimate','spent_time','requester','assigned','updated_dts'),
    			));
    }

}
