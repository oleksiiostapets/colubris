<?
class Model_TaskTime extends Model_Table {
    public $table='task_time';

    function init(){
        parent::init();

        $this->addField('task_id')->refModel('Model_Task')->mandatory(true);
        $this->addField('user_id')->refModel('Model_User')->mandatory(true);

        $this->addField('spent_time');

        $this->addField('comment')->dataType('text');

        $this->addField('created_dts');
        
        $this->addHook('beforeInsert', function($m,$q){
        	$q->set('created_dts', $q->expr('now()'));
        	$q->set('user_id', $q->api->auth->model['id']);
        });
        
       	$this->setOrder('created_dts',true);

	}
}
