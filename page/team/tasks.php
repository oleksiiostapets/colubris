<?php

class page_team_tasks extends Page {

    function initMainPage() {
    	$s=$this->add('View_Switcher');
    	
        $b=$this->add('Button')->set('New Task');
        $b->js('click', array(
        		$this->js()->univ()->redirect($this->api->url('team/tasks/new'))
        ));
        
        $cr=$this->add('CRUD',array('grid_class'=>'Grid_Tasks','allow_add'=>false,'allow_edit'=>true,'allow_del'=>true));
        $m=$this->add('Model_Task');
        if ($this->api->recall('project_id')>0) $m->addCondition('project_id',$this->api->recall('project_id'));
        if ($this->api->recall('quote_id')>0) {
        	$mq=$this->add('Model_Quote')->load($this->api->recall('quote_id'));
        	$m->addCondition('requirement_id','IN', explode(',',$mq->getRequirements_id()));
        }
        if ($this->api->recall('requirement_id')>0) $m->addCondition('requirement_id',$this->api->recall('requirement_id'));
        if ($this->api->recall('status')!='all') $m->addCondition('status',$this->api->recall('status'));
        if ($this->api->recall('assigned_id')>0) $m->addCondition('assigned_id',$this->api->recall('assigned_id'));
        $cr->setModel($m,
        		array('name','descr_original','priority','status','estimate','spent_time','assigned_id'),
        		array('name','descr_original','priority','status','estimate','spent_time','assigned')
        		);
        
        if($cr->grid){
        	$cr->grid->addColumn('expander','attachments');
        	$cr->grid->addFormatter('status','status');
        }
    }
    
    // "Expander" pages
    function page_attachments(){
    	$this->api->stickyGet('task_id');
      	$model=$this->add('Model_Attach')->addCondition('task_id',$_GET['task_id']);
       	$crud=$this->add('CRUD');
       	$crud->setModel($model,
       			array('description','file_id'),
       			array('description','file','updated_dts')
       	);
       	if($crud->grid){
       		$crud->grid->addFormatter('file','download');
       	}
    }
}
