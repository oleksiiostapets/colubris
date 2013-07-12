<?php
class Model_Quote extends Model_Table {
    public $table="quote";
    function init(){
        parent::init();
        $this->hasOne('Project')->display(array('form'=>'autocomplete/Basic'));
        //$this->addField('project_id')->refModel('Model_Project');
                //->display(array('form'=>'autocomplete/basic'));
        $this->hasOne('User');
        $this->addField('name')->mandatory('required');
        $this->addField('amount')->type('money')->mandatory(true);
        $this->addField('issued')->type('date');

        $this->addField('html')->type('text')->allowHtml(true);

        $this->addField('status')->setValueList(
            array(
                'quotation_requested'=>'quotation_requested',
                'estimate_needed'=>'estimate_needed',
            	'not_estimated'=>'not_estimated',
            	'estimated'=>'estimated',
            	'estimation_approved'=>'estimation_approved',
            	'finished'=>'finished',
            )
        )->mandatory('Cannot be empty');
        //$this->addField('attachment_id')->setModel('Model_Filestore_File');
        
        $this->addExpression('client_id')->set(function($m,$q){
            return $q->dsql()
                ->table('project')
                ->field('client_id')
                ->where('project.id',$q->getField('project_id'))
                ;
        });

        $this->addExpression('estimated')->set(function($m,$q){
            return $q->dsql()
                ->table('requirement')
                ->field('sum(estimate)')
                ->where('requirement.quote_id',$q->getField('id'))
                ;
        });
        
        $this->addExpression('spent_time')->set(function($m,$q){
            return $q->dsql()
                ->table('task')
                ->table('requirement')
                ->field('sum(task.spent_time)')
                ->where('requirement.id=task.requirement_id')
                ->where('requirement.quote_id',$q->getField('id'))
                ;
        });
        
    }
    
    function getRequirements(){
    	$rm=$this->add('Model_Requirement')->addCondition('quote_id',$this->get('id'));
    	return($rm->getRows());
    }
    function getRequirements_id(){
    	$rids='';
    	foreach($this->getRequirements() as $reqs){
    		if ($rids=='') $rids=$reqs['id']; else $rids.=','.$reqs['id'];
    	}
    	
    	return($rids);
    }
    
}
