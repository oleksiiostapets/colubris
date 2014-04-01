<?php
class View_TasksCRUD extends View {

    public $allow_add  = false;
    public $allow_edit = false;
    public $allow_del  = false;

    function init(){
        parent::init();

        $b=$this->add('Button')->set('New Task');
        $b->js('click', array(
            $this->js()->univ()->redirect($this->api->url('tasks/new'))
        ));

        $m=$this->add('Model_Task');
        $m=$this->addConditions($m);

        $cr=$this->add('CRUD',array(
            'form_class'=>'Form_EditTask',
            'grid_class'=>'Grid_Tasks',
            'allow_add'=>$this->allow_add,
            'allow_edit'=>$this->allow_edit,
            'allow_del'=>$this->allow_del)
        );
        $cr->setModel($m,
            $m->whatFieldsUserCanEdit($this->api->currentUser()),
            $m->whatFieldsUserCanSee($this->api->currentUser())
        );
        
        if($cr->grid){
            $cr->grid->addClass('zebra bordered');
            $cr->grid->add('View_ExtendedPaginator',
                array(
                    'values'=>array('10','50','100'),
                    'grid'=>$cr->grid,
                ),
                'extended_paginator');
//            $cr->grid->addPaginator(10);
        	$cr->grid->js('reload')->reload();

            $cr->grid->addQuickSearch(array('name'));

            if(!$this->api->currentUser()->isClient()){
                $cr->grid->addColumn('button','time');
                if ($_GET['time']) {
                    $this->js()->univ()->frameURL($this->api->_('Time'),array(
                        $this->api->url('./time',array('task_id'=>$_GET['time'],'reload_view'=>$cr->grid->name))
                    ))->execute();
                }
            }
        }
    }

    // add conditions according to filters
    function addConditions($m) {
        if (!is_null($this->api->recall('project_id')) && ($this->api->recall('project_id')>0)){
            $m->addCondition('project_id',$this->api->recall('project_id'));
        }
        if (!is_null($this->api->recall('quote_id')) && ($this->api->recall('quote_id')>0)) {
        	$mq = $this->add('Model_Quote')
                ->load($this->api->recall('quote_id'));
        	$m->addCondition('requirement_id','IN', explode(',',$mq->getRequirements_id()));
        }
        if (!is_null($this->api->recall('requirement_id')) && ($this->api->recall('requirement_id')>0)){
            $m->addCondition('requirement_id',$this->api->recall('requirement_id'));
        }
        if (!is_null($this->api->recall('status')) && ($this->api->recall('status')!='all')){
            $m->addCondition('status',$this->api->recall('status'));
        }
        if (!is_null($this->api->recall('assigned_id')) && ($this->api->recall('assigned_id')>0)){
            $m->addCondition('assigned_id',$this->api->recall('assigned_id'));
        }
        return $m;
    }
}
