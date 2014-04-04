<?php
class View_TasksCRUD extends View {

    function init(){
        parent::init();

//        $b = $this->add('Button')->set('New Task');
//        $b->js('click', array(
//            $this->js()->univ()->redirect($this->api->url('tasks/new'))
//        ));

        $m = $this->add('Model_Task');
        $m = $this->addConditions($m);

        $cr = $this->add('CRUD_Task',array(
            'form_class' => 'Form_EditTask',
            'grid_class' => 'Grid_Tasks',
            'allow_add'  => true,
            'allow_edit' => true,
            'allow_del'  => true
        ));
        $cr->setModel($m,
            $this->app->user_access->whatTaskFieldsUserCanEdit(),
            $this->app->user_access->whatTaskFieldsUserCanSee()
        );
        $cr->configure();
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
