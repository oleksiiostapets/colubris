<?php

class page_manager extends Page {
    function init(){
        parent::init();
        $this->title = 'Manager';
        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Manager',
                    'url' => 'manager',
                ),
            )
        ),'bread_crumb');
    }

    function page_index() {

        $cc=$this->add('Columns');
        $left=$cc->addColumn();
        $right=$cc->addColumn();

        $left->add('H2')->set('High-priority tasks');
        $right->add('H2')->set('Active Projects');

        $pr=$left->add('Manager_Projects');
       // if($pr->acceptance)$pr->acceptance->addFormatter('bugs','expander');
      //  if($pr->acceptance)$pr->acceptance->addFormatter('tasks','expander');
        $left->add('Manager_Tasks');

        $right->add('Manager_Deadlines');

        return;
        $right->add('Manager_Reports');




        $budget = $this->add('Model_Budget');
        $budget->getField('mandays')->caption('Quoted');
        return;
        $g = $this->add('ReportGrid', null, 'open_budgets');
        $m = $g->setModel($budget, array('name', 'days_spent', 'mandays', 'deadline'));
        $m->addCondition('accepted', true);
        $m->addCondition('closed', false);
        //$g->addColumn('text', 'quoted');
        $g->addColumn('html', 'difference', 'Difference %');
        $g->dq->order('coalesce(deadline,"2999-01-01") asc,id desc');
        //$g->dq->where('date>now()-interval 1 week');
        $g->addPaginator(10);

//
        $g = $this->add('Grid_ManagerDeveloperStats', null, 'developer_stats');
        $m = $g->setModel('Developer_Stats', array('id','name', 'hours_today', 'hours_lastday', 'hours_week', 'hours_lastweek', 'hours_month', 'hours_lastmonth'));
//  $g->dq->where('date>now()-interval 1 week');
        $g->addColumnPlain('expander', 'userprojects', 'View Projects');
        $g->addPaginator(10);
    }

    function page_tasks(){
        $this->api->redirect(
                $this->api->getDestinationURL('../bugs',
                    array(
                        'type'=>'bugs',
                        'cut_page'=>true,
                        'budget_id'=>$_GET['budget_id']
                        )));
    }

    function page_bugs(){
        $this->api->stickyGET('budget_id');
        $g=$this->add('Grid');
        $m=$g->setModel('Task_Bug',array('name','priority','estimate','status'));
        $g->addColumn('button','info');
        $m->addCondition('budget_id',$_GET['budget_id']);

        if($_GET['info']){
            $this->js()->univ()->dialogURL('Task Information',
                    $this->api->getDestinationURL('./info',
                        array('id'=>$_GET['info'])))
                ->execute();
        }
    }
    function page_bugs_info(){
        $cc=$this->add('Columns');
        $left=$cc->addColumn();
        $right=$cc->addColumn();

        $left->add('H3')->set('Information');
        $right->add('H3')->set('Client Interaction');


        $form=$left->add('Form');
        $form->setModel('Task')->loadData($_GET['id']);



        $form=$right->add('Form');
        $form->addField('radio','type','')
            ->setValueList(array('upadate'=>'update','question'=>'question (Requires Client\'s feedback)'));
        $form->addField('text','note','');
        $form->addField('checkbox','urgent');
        $form->setFormClass('vertical');

        $right->add('H3')->set('History');

    }

    function page_userprojects() {
        $this->api->stickyGET('id');
        $g = $this->add('Grid');
        $m = $g->setModel('Timesheet', array( 'budget', 'title', 'date', 'minutes'));
        $m->addCondition('user_id', $_GET['id']);
        $g->dq->where('date>now()-interval 1 month');
        $g->dq->order('date desc,id desc');
        $g->addPaginator(10);
    }

    function page_amount() {

        $this->api->stickyGET('id');

        $t = $this->add('Tabs');

        $g = $t->addTab('Breakdown')
                        ->add('ReportGrid');
        $g->setModel('Timesheet')
                ->addCondition('report_id', $_GET['id']);
        $g->addTotals();

        $t->addTab('Amend')
                ->add('FormAndSave')->setModel('Report', array('user_id', 'client_id', 'budget_id', 'date', 'amount'))->loadData($_GET['id']);

        $t->addTab('Delete')
                ->add('FormDelete')->setModel('Report', array(''))->loadData($_GET['id']);
        ;
    }
    function defaultTemplate() {
        return array('page/page');
    }

    /*
    function defaultTemplate() {
        if ($this->api->page == 'manager'

            )return array('page/manager');
        return parent::defaultTemplate();
    }

    */
}
