<?php
class Model_Requirement extends Model_Auditable {
	public $table='requirement';
	function init(){
		parent::init();//$this->debug();
		$this->hasOne('Quote');
		$this->hasOne('User')->Caption('Creator');
		$this->addField('name')->mandatory('required');
		$this->addField('descr')->type('text');
		$this->addField('estimate');
		$this->addField('is_included')->defaultValue(false)->type('boolean')->mandatory('required');
		//$this->add('filestore\Field_File','file_id')->setModel('filestore/File');

		$this->add('filestore/Field_File', array(
			'name'=>'file_id',
			'use_model'=>'Model_Myfile'
		));

		$this->addField('is_deleted')->defaultValue('0')->type('boolean')->mandatory('required');
//        $this->addField('deleted_id')->refModel('Model_User');
		$this->hasOne('User','deleted_id');

		$this->addExpressions();

		$this->addHooks();
	}

	// ------------------------------------------------------------------------------
	//
	//            HOOKS :: BEGIN
	//
	// ------------------------------------------------------------------------------

	function addHooks() {
		$this->addHook('beforeDelete', function($m){
			$m['deleted_id']=$m->api->currentUser()->get('id');
		});
	}

	function addExpressions(){
		$this->addExpression('project_id')->set(function($m,$q){
			return $q->dsql()
				->table('quote')
				->field('project_id')
				->where('quote.id',$q->getField('quote_id'))
				;
		});

        $this->addExpression('project_name')->set(function($m,$q){
            return $q->dsql()
                ->table('project')
                ->table('quote')
                ->field('project.name')
                ->where('quote.id',$q->getField('quote_id'))
                ->where('quote.project_id=project.id')
                ;
        });

        $this->addExpression('spent_time')->set(function($m,$q){
			return $q->dsql()
				->table('task')
				->table('task_time')
				->field('sum(task_time.spent_time)')
				->where('task.id=task_time.task_id')
				->where('task.requirement_id',$q->getField('id'))
				->where('task_time.remove_billing',0)
				;
		});
		$this->addExpression('count_comments')->set(function($m,$q){
			return $q->dsql()
				->table('reqcomment')
				->field('count(id)')
				->where('reqcomment.requirement_id',$q->getField('id'))
				->where('reqcomment.is_deleted',false)
				;
		});
	}

	function deleted() {
		//$this->addCondition('organisation_id',$this->app->currentUser()->get('organisation_id'));
		$this->addCondition('is_deleted',true);
		return $this;
	}
	function notDeleted() {
		$this->addCondition('is_deleted',false);
		return $this;
	}

}
