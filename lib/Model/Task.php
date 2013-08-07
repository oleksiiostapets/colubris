<?
class Model_Task extends Model_Table {
    public $table='task';

    function init(){
        parent::init();

        $this->addField('name')->mandatory(true);
        $this->addField('priority')->setValueList(
            array(
                'low'=>'low',
                'normal'=>'normal',
            	'high'=>'high',
            )
        )->defaultValue('normal');

        $this->addField('status')->setValueList($this->api->task_statuses)->defaultValue('unstarted');
        
        $this->addField('descr_original')->dataType('text');

        $this->addField('estimate')->dataType('money');
        //$this->addField('spent_time')->dataType('int');

        //$this->addField('deviation')->dataType('text');

        $this->addField('project_id')->refModel('Model_Project')->mandatory(true);
        $this->addField('requirement_id')->refModel('Model_Requirement');
        $this->addField('requester_id')->refModel('Model_User');
        $this->addField('assigned_id')->refModel('Model_User');
        
        $this->addField('created_dts');
        $this->addField('updated_dts');
        
        $this->addHook('beforeInsert', function($m,$q){
        	$q->set('created_dts', $q->expr('now()'));
        });
        
       	$this->addHook('beforeSave', function($m){
       		$m['updated_dts']=date('Y-m-d G:i:s', time());

       		$to='';
        	if ($m->get('requester_id')>0){
        		$u=$m->add('Model_User')->load($m->get('requester_id'));
        		if ($u['email']!='') $to=$u['email'];
        	}
        	if ($m->get('assigned_id')>0){
        		$u=$m->add('Model_User')->load($m->get('assigned_id'));
        		if ($u['email']!=''){
        			if ($to=='') $to=$u['email']; else $to.=', '.$u['email'];
        		}
        	}
        	if ($to!=''){
        		$m->api->mailer->sendMail($to,'edit_task',array(
        				'link'=>$m->api->url('/manager/tasks'),
        				'task_name'=>$m->get('name'),
        				));
        	}
       	});
        
       	$this->setOrder('updated_dts',true);

        $this->addExpression('spent_time')->set(function($m,$q){
            return $q->dsql()
                ->table('task_time')
                ->field('sum(task_time.spent_time)')
                ->where('task_time.task_id',$q->getField('id'))
                ;
        });
    }
}
