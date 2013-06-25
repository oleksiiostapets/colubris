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
	}
}
