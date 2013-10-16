<?
class Model_Requirement_Base extends Model_Auditable {
    public $table='requirement';
    function init(){
        parent::init();
        $this->hasOne('Quote');
        $this->hasOne('User')->Caption('Creator');
        $this->addField('name')->mandatory('required');
        $this->addField('descr')->type('text');
        $this->addField('estimate');
        $this->addField('is_included')->defaultValue('1')->type('boolean')->mandatory('required');
        //$this->add('filestore\Field_File','file_id')->setModel('filestore/File');

        $this->add('filestore/Field_File', array(
            'name'=>'file_id',
            'use_model'=>'Model_Myfile'
        ));

        $this->addExpression('project_id')->set(function($m,$q){
            return $q->dsql()
                ->table('quote')
                ->field('project_id')
                ->where('quote.id',$q->getField('quote_id'))
                ;
        });

        $this->addExpression('spent_time')->set(function($m,$q){
            return $q->dsql()
                ->table('task')
                ->table('task_time')
                ->field('sum(task_time.spent_time)')
                ->where('task.id=task_time.task_id')
                ->where('task.requirement_id',$q->getField('id'))
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
        $this->addField('is_deleted')->defaultValue('0')->type('boolean')->mandatory('required');
        $this->addField('deleted_id')->refModel('Model_User');
        $this->addHook('beforeDelete', function($m){
            $m['deleted_id']=$m->api->currentUser()->get('id');
        });
    }
}
