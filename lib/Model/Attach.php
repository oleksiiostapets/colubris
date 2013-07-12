<?
class Model_Attach extends Model_Table {
	public $table='attach';
	function init(){
		parent::init();
		$this->hasOne('Task');
		$this->addField('description')->type('text');
		$this->add('filestore\Field_File','file_id')->setModel('filestore/File')->mandatory(true);

        $this->addField('created_dts');
        $this->addField('updated_dts');
        
        $this->addHook('beforeInsert', function($m,$q){
        	$q->set('created_dts', $q->expr('now()'));
        });
        
       	$this->addHook('beforeSave', function($m){
       		$m['updated_dts']=date('Y-m-d G:i:s', time());
       	});
        
       	$this->setOrder('updated_dts',true);
	}
}
