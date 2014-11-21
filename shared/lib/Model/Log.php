<?php
class Model_Log extends Model_Table {
	public $table="log";
	function init(){
		parent::init();
		
		$this->addField('new_data');
		$this->addField('changed_fields');
		$this->addField('class');
		$this->addField('rec_id');
		$this->addField('created_at');
		$this->hasOne('User','user_id','email');
//        $this->addField('organisation_id')->refModel('Model_Organisation');
        $this->hasOne('Organisation','organisation_id');
        //$this->addCondition('organisation_id',$this->app->currentUser()->get('organisation_id'));

        $this->addHook('beforeInsert',function($m,$q){
            $q->set('created_at',$q->expr('now()'));
        });
		
		$this->addHook('beforeSave',function($m){
            if( !isset($this->app->is_test_app)) $m['user_id']=$this->app->currentUser()->get('id');
		});
		
	}

    // API methods
    function prepareForSelect(Model_User $u){
        $r = $this->add('Model_User_Right');

        $fields = ['id'];

        if($r->canSeeLogs($u['id'])){
            $fields = array('id','new_data','changed_fields','class','rec_id','created_at','user_id','user','organisation_id','organisation');
        }else{
            throw $this->exception('This User cannot see logs','API_CannotSee');
        }

        $this->setActualFields($fields);
        return $this;
    }

}