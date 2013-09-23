<?php
class View_Dashboard extends View {
    public $allow_add  = false;
    public $allow_edit = false;
    public $allow_del  = false;
    function init(){
        parent::init();

        $this->add('P');

        $v=$this->add('View')->setClass('left span6');
        $v->add('H2')->set('Comments to quotes');
        $cr=$v->add('CRUD',array('grid_class'=>'Grid_Dashcomments','allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));

        if ($this->api->currentUser()->isClient()) $m=$this->add('Model_Reqcomment_Client');
        elseif ($this->api->currentUser()->isDeveloper()) $m=$this->add('Model_Reqcomment_Developer');
        else $m=$this->add('Model_Reqcomment');
        $m->setOrder('created_dts',true);

        $jr = $m->join('requirement.id','requirement_id','left','_req');
        $jr->addField('requirement_name','name');
        $jr->addField('quote_id','quote_id');

        $jq = $jr->join('quote.id','quote_id','left','_quote');
        $jq->addField('quote_name','name');
        $jq->addField('quote_status','status');

        $jp = $jq->join('project.id','project_id','left','_pr');
        $jp->addField('project_name','name');
        $jp->addField('organisation_id','organisation_id');
        $m->addCondition('organisation_id',$this->api->auth->model['organisation_id']);

        $m->addCondition('quote_status','IN',array('quotation_requested','estimate_needed','not_estimated','estimated'));

        $cr->setModel($m,
            array('text','file_id'),
            array('text','user','file','file_thumb','created_dts','project_name','quote_name','quote_status','requirement_name','quote_id')
        );

        if ($cr->grid){
            $cr->grid->addPaginator(5);
            $cr->grid->addFormatter('project_name','wrap');
        }


        $v=$this->add('View')->setClass('right span6');
        $v->add('H2')->set('Comments to tasks');

        $cr=$v->add('CRUD',array('grid_class'=>'Grid_Dashcomments','allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));

        if ($this->api->currentUser()->isClient()) $m=$this->add('Model_Taskcomment_Client');
        elseif ($this->api->currentUser()->isDeveloper()) $m=$this->add('Model_Taskcomment_Developer');
        else $m=$this->add('Model_Taskcomment');
        $m->setOrder('created_dts',true);

        $jt = $m->join('task.id','task_id','left','_t');
        $jt->addField('task_name','name');

        $jp = $jt->join('project.id','project_id','left','_pr');
        $jp->addField('project_name','name');
        $jp->addField('organisation_id','organisation_id');
        $m->addCondition('organisation_id',$this->api->auth->model['organisation_id']);

        $cr->setModel($m,
            array('text','file_id'),
            array('text','user','file','file_thumb','created_dts','project_name','task_name','task_id')
        );

        if ($cr->grid){
            $cr->grid->addPaginator(5);
            $cr->grid->addFormatter('project_name','wrap');
            //$cr->grid->addFormatter('task_name','wrap');
        }

        $v=$this->add('View')->setClass('clear');


        $this->add('HR');

        $this->add('H2')->set('My active tasks (requested by me or assigned to me)');
        $cr=$this->add('CRUD',array('grid_class'=>'Grid_Tasks','allow_add'=>$this->allow_add,'allow_edit'=>$this->allow_edit,'allow_del'=>$this->allow_del));
        $m=$this->add('Model_Task');
        if (!$_GET['submit']) {
            $m->addCondition('status','<>','accepted');
        }
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
            $cr->grid->addFormatter('name','wrap');
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
            $cr->grid->addPaginator(10);

        }
    }
}
