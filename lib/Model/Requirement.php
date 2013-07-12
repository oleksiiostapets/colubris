<?
class Model_Requirement extends Model_Table {
	public $table='requirement';
	function init(){
		parent::init();
		$this->hasOne('Quote');
		$this->hasOne('User');
		$this->addField('name')->mandatory('required');
		$this->addField('descr')->type('text');
		$this->addField('estimate');
		$this->add('filestore\Field_File','file_id')->setModel('filestore/File');

        $this->addExpression('spent_time')->set(function($m,$q){
            return $q->dsql()
                ->table('task')
                ->field('sum(spent_time)')
                ->where('task.requirement_id',$q->getField('id'))
                ;
        });
	}
}
