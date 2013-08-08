<?php
class View_Dashboard extends View {
    function init(){
        parent::init();

        $this->add('H2')->set('My active tasks (requested by me or assigned to me)');
        $cr=$this->add('CRUD',array('grid_class'=>'Grid_Tasks','allow_add'=>$this->allow_add,'allow_edit'=>$this->allow_edit,'allow_del'=>$this->allow_del));
        $m=$this->add('Model_Task');
        $m->addCondition('status','<>','accepted');
        $q=$m->_dsql();
        $q->where($q->orExpr()
        		->where('requester_id',$this->api->auth->model['id'])
        		->where('assigned_id',$this->api->auth->model['id'])
        );

        $cr->setModel($m,
        		$this->edit_fields,
        		$this->show_fields
        		);
        
		if($cr->grid){
        	$cr->grid->js('reload')->reload();
        	
        	if(!$this->api->auth->model['is_client']){
   	        	$cr->grid->addColumn('button','time');
	            if ($_GET['time']) {
	                $this->js()->univ()->frameURL($this->api->_('Time'),array(
	                    $this->api->url('./time',array('task_id'=>$_GET['time'],'reload_view'=>$cr->grid->name))
	                ))->execute();
	            }
        	}
/*
            $cr->grid->addColumn('button','attachments');
            if ($_GET['attachments']) {
                $this->js()->univ()->frameURL($this->api->_('Attachments'),array(
                    $this->api->url('./attachments',array('task_id'=>$_GET['attachments'],'reload_view'=>$cr->grid->name))
                ))->execute();
            }
*/
            $cr->grid->addColumn('expander','more');
            
        	$cr->grid->addFormatter('status','status');
        }
    }
}
