<?
class Model_Task extends Model_Table {
    public $table='task';

    function init(){
        parent::init();

        //$this->debug();

        $this->addField('name')->mandatory(true);
        $this->addField('priority')->setValueList(
            array(
                'low'=>'low',
                'normal'=>'normal',
            	'high'=>'high',
            )
        )->defaultValue('normal');

        $this->addField('status')->setValueList($this->api->task_statuses)->defaultValue('unstarted')->sortable(true);
        $this->addField('type')->setValueList($this->api->task_types)->defaultValue('change request')->sortable(true);

        $this->addField('descr_original')->dataType('text');

        $this->addField('estimate')->dataType('money');
        //$this->addField('spent_time')->dataType('int');

        //$this->addField('deviation')->dataType('text');

        $this->addField('project_id')->refModel('Model_Project')->mandatory(true)->sortable(true);
        $this->addField('requirement_id')->refModel('Model_Requirement');
        $this->addField('requester_id')->refModel('Model_User');
        $this->addField('assigned_id')->refModel('Model_User');

        if($this->api->auth->model['is_client']){
            $j = $this->join('project.id','project_id','left','_p');
            $j->addField('client_id','client_id');
            $this->addCondition('client_id',$this->api->auth->model['client_id']);
        }

        $this->addField('created_dts');
        $this->addField('updated_dts')->caption('Updated')->sortable(true);
        
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
        		$m->api->mailer->sendMail($to,'task_edit',array(
        				'link'=>$m->api->siteURL().$m->api->url($m->api->getUserType().'/tasks'),
        				'task_name'=>$m->get('name'),
        				),'mail_task_changes');
        	}
       	});
       	
       	$this->addHook('beforeDelete', function($m){
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
        		$m->api->mailer->sendMail($to,'task_delete',array(
        				'link'=>$m->api->siteURL().$m->api->url($m->api->getUserType().'/tasks'),
        				'task_name'=>$m->get('name'),
        				),'mail_task_changes');
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
