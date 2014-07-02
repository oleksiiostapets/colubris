<?php
class Model_TaskTime extends Model_Table {
    public $table='task_time';

    function init(){
        parent::init();

        $this->addField('task_id')->refModel('Model_Task')->mandatory(true);
        $this->addField('user_id')->refModel('Model_User')->mandatory(true);

        $this->addField('spent_time')->mandatory(true);

        $this->addField('comment')->dataType('text');

        $this->addField('date')->dataType('date');

        $this->addField('remove_billing')->type('boolean')->defaultValue('0')->caption('Remove from billing');

        $this->addHook('beforeInsert', function($m,$q){
        	if($m['date']=='') $q->set('date', $q->expr('now()'));
        	$q->set('user_id', $q->api->auth->model['id']);
        });
        
       	$this->setOrder('date',true);

	}
}
