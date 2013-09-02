<?php

class page_projectsfunctions extends Page {
    function page_tasks(){
        $this->api->stickyGET('project_id');
        $cr=$this->add('CRUD',array('grid_class'=>'Grid_Tasks'));
        $m=$this->add('Model_Task')
            ->addCondition('project_id',$_GET['project_id']);
        $cr->setModel($m,
            array('name','descr_original','estimate','priority','type','status','requester_id','assigned_id'),
            array('name','estimate','priority','type','status','spent_time','requester','assigned')
        );
        if($cr->grid){
            $cr->grid->addFormatter('status','status');
        }
    }

}
