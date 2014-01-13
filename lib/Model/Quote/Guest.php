<?php
class Model_Quote_Guest extends Model_Table {
    public $table="quote";
    function init(){
        parent::init(); //$this->debug();
        $this->hasOne('Project_Guest')->mandatory('required');

        $this->hasOne('User');
        $this->addField('name')->mandatory('required');
        $this->addField('general_description')->type('text')->allowHtml(true);
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

        $this->addField('rate')->defaultValue('0.00');
        $this->addField('currency')->setValueList(
            array(
                'EUR'=>'EUR',
                'GBP'=>'GBP',
                'USD'=>'USD',
            )
        )->mandatory('Cannot be empty');

        $this->addField('is_deleted')->type('boolean')->defaultValue('0');
        $this->addField('deleted_id')->refModel('Model_User');
        $this->addHook('beforeDelete', function($m){
            $m['deleted_id']=$m->api->currentUser()->get('id');
        });

        $this->addField('organisation_id')->refModel('Model_Organisation');

        $this->addField('created_dts');
        $this->addField('updated_dts')->caption('Updated')->sortable(true);
        $this->addField('expires_dts')->caption('Expires');

        $this->addHook('beforeInsert', function($m,$q){
            $q->set('created_dts', $q->expr('now()'));
            $q->set('expires_dts', $q->expr('DATE_ADD(NOW(), INTERVAL 1 MONTH)'));
        });

        $this->addHook('beforeSave', function($m){
            $m['updated_dts']=date('Y-m-d G:i:s', time());
        });

        $this->addExpression('client_id')->set(function($m,$q){
            return $q->dsql()
                ->table('project')
                ->field('client_id')
                ->where('project.id',$q->getField('project_id'))
                ;
        });
    }
}
