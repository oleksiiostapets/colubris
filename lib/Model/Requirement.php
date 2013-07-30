<?
class Model_Requirement extends Model_Table {
	public $table='requirement';
	function init(){
		parent::init();
		$this->hasOne('Quote');
		$this->hasOne('User')->Caption('Creator');
		$this->addField('name')->mandatory('required');
		$this->addField('descr')->type('text');
		$this->addField('estimate');
		//$this->add('filestore\Field_File','file_id')->setModel('filestore/File');
		
		$this->add('filestore/Field_File', array(
				'name'=>'file_id',
				'use_model'=>'Model_Myfile'
		));
		
        $this->addExpression('spent_time')->set(function($m,$q){
            return $q->dsql()
                ->table('task')
                ->field('sum(spent_time)')
                ->where('task.requirement_id',$q->getField('id'))
                ;
        });
        $this->addExpression('count_comments')->set(function($m,$q){
            return $q->dsql()
                ->table('reqcomment')
                ->field('count(id)')
                ->where('reqcomment.requirement_id',$q->getField('id'))
                ;
        });
	}
}
