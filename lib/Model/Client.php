<?
class Model_Client extends Model_Table {
    public $table='client';

    function init(){
        parent::init();

        $this->newField('name');

        $this->addField('smbo_id')->type('int');
        $this->addField('email');
        $this->addField('is_archive')->type('boolean');

        $this->addField('total_sales')->type('money');
        $this->addField('ebalance')->type('int');
        $this->addField('day_credit')->type('int');
/*
        $this->newField('project_count')
            ->caption('Projects')
            ->calculated(true);

        $this->newField('total_budgets')
            ->type('money')
            ->calculated(true);

        $this->newField('total_expense')
            ->type('money')
            ->calculated(true);
 * 
 */
    }
/*
    function calculate_project_count(){
        return $this->add('Model_Project')
            ->dsql()
            ->field('count(*)')
            ->where('pr.client_id=cl.id')
            ->select()
            ;
    }
    function calculate_total_budgets(){
        return $this->add('Model_Budget')
            ->addCondition('accepted',true)
            ->dsql()
            ->field('sum(amount)')
            ->join('project pr','pr.id=bu.project_id','left')
            ->where('pr.client_id=cl.id')
            ->select()
            ;
    }
    function calculate_total_expense(){
		return $this->add('Model_Timesheet')
			->dsql()
            ->join('payment pa','pa.user_id=T.user_id and pa.budget_id=T.budget_id')
            ->join('budget bu','bu.id=T.budget_id')
            ->join('project pr','pr.id=bu.project_id')
			->field('sum(T.minutes/60*pa.hourly_rate)')
            ->where('cl.id=pr.client_id')
			->select();


        return 1;
    }
    */
}
