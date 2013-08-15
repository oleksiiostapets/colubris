<?
class Model_TaskTime extends Model_Table {
    public $table='task_time';

    function init(){
        parent::init();

        $this->addField('task_id')->refModel('Model_Task')->mandatory(true);
        $this->addField('user_id')->refModel('Model_User_Notdeleted')->mandatory(true);

        $this->addField('spent_time');

        $this->addField('comment')->dataType('text');

        $this->addField('date')->dataType('date');
        
        $this->addHook('beforeInsert', function($m,$q){
        	if($m['date']=='') $q->set('date', $q->expr('now()'));
        	$q->set('user_id', $q->api->auth->model['id']);
        });
        
       	$this->setOrder('date',true);

	}
}
