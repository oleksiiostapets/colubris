<?
class Model_Reqcomment extends Model_Table {
	public $table='reqcomment';
	function init(){
		parent::init();
		$this->hasOne('Requirement');
		$this->hasOne('User')->Caption('Creator');
		$this->addField('text')->type('text')->mandatory('required');
		
		$this->add('filestore/Field_File', array(
				'name'=>'file_id',
				'use_model'=>'Model_Myfile'
		));
		
		
		$this->addHook('beforeInsert',function($m,$q){
			$q->set('user_id',$q->api->auth->model['id']);
		});
		
        $this->addHook('beforeSave',function($m){
        	if($m['user_id']>0){
	        	if($m->api->auth->model['id']!=$m['user_id']){
	        		throw $m
		                ->exception('You have no permissions to do this','ValidityCheck')
		                ->setField('text');
	        	}
        	}
        });
        $this->addHook('beforeDelete',function($m){
        	if($m['user_id']>0){
	        	if($m->api->auth->model['id']!=$m['user_id']){
	        		throw $m
		                ->exception('You have no permissions to do this','ValidityCheck');
	        	}
        	}
        });
	}
}
