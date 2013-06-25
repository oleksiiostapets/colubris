<?
class Model_Reqcomment extends Model_Table {
	public $table='reqcomment';
	function init(){
		parent::init();
		$this->hasOne('Requirement');
		$this->hasOne('User');
		$this->addField('text')->mandatory('required');
		
		$this->addHook('beforeInsert',function($m,$q){
			$q->set('user_id',$q->api->auth->model['id']);
		});
		
	}
}
