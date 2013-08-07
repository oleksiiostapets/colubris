<?php

class page_team_dashboard extends page_dashboard {

    function initMainPage() {
    	$this->add('View_Dashboard',array(
    			'allow_add'=>false,'allow_edit'=>true,'allow_del'=>true,
    			'edit_fields'=>array('name','descr_original','priority','status','estimate','requester_id','assigned_id'),
    			'show_fields'=>array('name','priority','status','estimate','spent_time','requester','assigned'),
    			));
    }

}
