<?
class Model_Budget extends Model_Table {
    public $table='budget';
    function init(){
        parent::init();

        $this->addField('name')->sortable(true);

        $this->addField('priority')->type('list')->listData(array(
                    'Select...',
                    '0-none'=>'0 - Waiting on action from client',
                    '1-low'=>'1 - Work on project in free time',
                    '2-schedule'=>'2 - Work according to schedule',
                    '3-normal'=>'3 - Work to complete at earliest',
                    '4-urgent'=>'4 - Urgent, work fast, call if blockers',
                    '5-overtime'=>'5 - Critical, working over-time, possible penalties',
                    '6-nosleep'=>'6 - Not going to sleep until finished'
                    ))->defaultValue('3-normal')
                    ;
                        

        $this->addField('state')->type('list')->listData(array(
                    'Select...',
                    'irrelevant'=>'State is not applicable',
                    'lead'=>'01 - Expressed interest in project',
                    'discuss'=>'02 - Had brief discussion about project',
                    'quote'=>'03 - Expects quotation',
                    'quotereview'=>'04 - Internal review of quotation',
                    'quoteready'=>'05 - Quote issued',
                    'mutual'=>'06 - Mutually agreed on quote, deposit payable',
                    'deposited'=>'10 - Initial deposit sent',
                    'gotdeposit'=>'11 - Deposit confirmed, preparing contract',
                    'papers'=>'12 - Contract signed, timeline and spec agreed',
                    'devel'=>'13 - Development started',
                    'develstop'=>'14 - Development suspended',
                    'qa'=>'15 - Internal Q/A',
                    'clientqa'=>'20 - Functionality Completed. Client Q/A',
                    'bugfixes'=>'21 - Feedback received, working/fixing',
                    'acceptance'=>'22 - Client feedback addressed. Acceptance',
                    'deployment'=>'23 - Waiting for installation instrucitons',
                    'launced'=>'30 - Deployed. 30-day warranty',
                    'support'=>'31 - On-going support',
                    'completed'=>'99 - SUCCESS! More work? Let us know!',
                    ))->defaultValue('lead')
                    ;

        $this->newField('start_date')->datatype('date')->sortable(true)
            ->defaultValue(date('Y-m-d',strtotime('next monday')));

        $this->newField('deadline')->datatype('date')->sortable(true);

        $this->newField('accepted')->datatype('boolean')
            ->sortable(true);

        $this->newField('closed')->datatype('boolean')
            ->sortable(true);

        $this->addfield('is_moreinfo_needed')->type('boolean')
            ->caption('Waiting for more information from client');

        $this->addField('is_delaying')->type('boolean')
            ->caption('Project is behind schedule');

        $this->addField('is_overtime')->type('boolean')
            ->caption('Project was underquoted');

        $this->newField('amount')
            ->caption('Sell Price')
            ->sortable(true);

        $this->addField('amount_paid')->type('money')->caption('Paid to date');

        $this->addField('quote_id')
            ->refModel('Model_Quote');

        $this->newField('expenses')
            ->caption('Development Cost')
            ->datatype('money')->sortable(true);

        $this->newField('expenses_descr')->datatype('text')
            ->caption('Notes');


        $this->newField('amount_spent')->datatype('money')->calculated(true)->sortable(true);

        $this->addField('currency')->type('list')->listData(array(
                    'Select...',
                    'EUR'=>'Euros',
                    'GBP'=>'UK Pounds',
                    'USD'=>'US Dollars'
                    ))->defaultValue('EUR')
            ;


        $this->newField('success_criteria')
                ->datatype('list')
                ->listData(array(
                        1=>'Requirements completed',
                        2=>'Mandays worked',
                        3=>'Budget depleted',
                        4=>'Deadline Reached',
                ))
            ;
        $this->newField('mandays')
                ->datatype('int')
            ;
        $this->newField('cur_mandays')
                ->datatype('int')
                ->calculated(true)
            ;

        $this->newField('days_spent')
			->datatype('int')
			->calculated(true)
            ;
        $this->newField('days_spent_lastweek')
			->datatype('int')
            ->caption('Days Spent Last Week')
			->calculated(true)
            ;

        $this->newField('project_id')
            ->sortable(true)
            ->refModel('Model_Project')
            ;
        $this->addField('client')
            ->sortable(true)
            ->calculated(true);

        $this->addField('team')
            ->sortable(true)
            ->calculated(true);

        $this->addField('total_pct')->type('int');
        $this->addField('timeline_html')->type('text');

    }
    function calculate_client(){
        return $this->add('Model_Client')
            ->dsql()
            ->join('project pr','cl.id=pr.client_id')
            ->field('cl.name')
            ->limit(1)
            ->where('pr.id=bu.project_id')
            ->select();
    }
    function calculate_team(){
        return $this->add('Model_Payment')
            ->dsql()
            ->where('pa.budget_id=bu.id')
            ->field('count(*)')
            ->select();

    }
    function scopeFilter(){
            if($sc=$this->api->recall('scope')){
                    if($sc['budget'])$this->addCondition('id',$sc['budget']);
            }
    }
    function calculate_cur_mandays(){
            return $this->add('Model_Timesheet')
                    ->dsql()
                    ->field('round(sum(T.minutes/60/8),1)')
                    ->where('T.budget_id=bu.id')
                    ->select();
    }
    function calculate_amount_spent(){
		return $this->add('Model_Timesheet')
			->dsql()
            ->join('payment pa','pa.user_id=T.user_id')
			->field('coalesce(sum(T.minutes/60*pa.hourly_rate),0)+coalesce(bu.expenses,0)')
			->where('T.budget_id=bu.id')
            ->where('pa.budget_id=bu.id')
			->select();
    }
        function calculate_days_spent(){
		return $this->add('Model_Timesheet')
			->dsql()
			->field('sum(T.minutes)/60/8')
			->where('T.budget_id=bu.id')
			->select();
	}
        function calculate_days_spent_lastweek(){
		return $this->add('Model_Timesheet')
			->dsql()
			->field('sum(T.minutes)/60/8')
			->where('T.budget_id=bu.id')
                        ->where(" YEARWEEK(date) = YEARWEEK(CURRENT_DATE - INTERVAL 7 DAY) ")
			->select();
	}
         
}
