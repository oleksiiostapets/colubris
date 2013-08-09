<?php
class Model_Quote extends Model_Table {
    public $table="quote";
    function init(){
        parent::init(); //$this->debug();
        $this->hasOne('Project')->display(array('form'=>'Form_Field_AutoEmpty'))->mandatory('required');
        //$this->addField('project_id')->refModel('Model_Project');
                //->display(array('form'=>'autocomplete/basic'));
        $this->hasOne('User');
        $this->addField('name')->mandatory('required');
        $this->addField('general')->type('text')->allowHtml(true);
        $this->addField('amount')->type('money')->mandatory(true);
        $this->addField('issued')->type('date');

        $this->addField('duration')->type('int');
        $this->addField('deadline')->type('date')->caption('Duration/Deadline');
        
        $this->addExpression('durdead')->caption('Duration(days)/Deadline')->set(function($m,$q){
            return $q->dsql()
                    ->expr('if(deadline is null,duration,deadline)');
        });
        
        $this->addField('html')->type('text')->allowHtml(true);

        $this->addField('status')->setValueList(
            array(
                'quotation_requested'=>'Quotation Requested',
                'estimate_needed'=>'Estimate Needed',
            	'not_estimated'=>'Not Estimated',
            	'estimated'=>'Estimated',
            	'estimation_approved'=>'Estimation Approved',
            	'finished'=>'Finished',
            )
        )->mandatory('Cannot be empty')->sortable(true);
        //$this->addField('attachment_id')->setModel('Model_Filestore_File');
        
        $this->addField('rate');
        $this->addField('currency')->setValueList(
        		array(
        				'EUR'=>'EUR',
        				'GBP'=>'GBP',
        				'USD'=>'USD',
        		)
        )->mandatory('Cannot be empty');

        $this->addExpression('client_id')->set(function($m,$q){
            return $q->dsql()
                ->table('project')
                ->field('client_id')
                ->where('project.id',$q->getField('project_id'))
                ;
        });

        $this->addExpression('estimated')->caption('Est.time(hours)')->set(function($m,$q){
            return $q->dsql()
                ->table('requirement')
                ->field('sum(estimate)')
                ->where('requirement.quote_id',$q->getField('id'))
                ;
        });
        
        $this->addExpression('estimpay')->caption('Est.pay')->set(function($m,$q){
            return $q->dsql()
                ->table('requirement')
                ->field('sum(estimate)*'.$q->getField('rate'))
                ->where('requirement.quote_id',$q->getField('id'))
                ;
        });
        
        $this->addExpression('spent_time')->set(function($m,$q){
            return $q->dsql()
                ->table('task')
                ->table('task_time')
                ->table('requirement')
                ->field('sum(task_time.spent_time)')
                ->where('requirement.id=task.requirement_id')
                ->where('task.id=task_time.task_id')
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

class Form_Field_AutoEmpty extends autocomplete\Form_Field_Basic {
    public $min_length = -1;
    public $hint = 'Ckick to see list of projects. Search results will be limited to 20 records.';
	function init(){
		parent::init();
        $this->other_field->js('click',array(
                $this->other_field->js()->autocomplete( "search", "" ),
            )
        );
	}
}