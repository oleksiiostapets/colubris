<?php

class Model_Developer_Stats extends Model_Developer {

    public $dr = array();

    function init() {
        parent::init();
        $this->addField('hours_today')
                ->caption('Hours Spent Today')
                ->calculated(true);
        $this->addField('hours_week')
                ->caption('This Week')
                ->calculated(true);
        $this->addField('hours_lastweek')
                ->caption('Last Week')
                ->calculated(true);

        $this->addField('hours_month')
                ->caption('This Month')
                ->calculated(true);
        $this->addField('hours_lastmonth')
                ->caption('Last Month')
                ->calculated(true);
    }

    function setDateRange($from, $to) {
        $this->dr = array($from, $to);
    }

    function applyDateRange($q) {
        if (!$this->dr

            )$this->dr = array(date('Y-m-d'), date('Y-m-d'));
        $q->where('date(date)>=', $this->dr[0]);
        $q->where('date(date)<=', $this->dr[1]);
    }

    function calculate_hours_today() {
     //   $date = date('Y-m-d');
        $q = $this->add('Model_Timesheet')->dsql();
        $q->where('T.user_id=u.id');
        $q->field('round(sum(minutes)/60)');
        $q->where('date(date)>=', date('Y-m-d'));
        $q->where('date(date)<=', date('Y-m-d'));

        //->where('date>now() - interval 1 day')
        // ->where('date(date)>=', $date)
        return $q->select();
    }

    function calculate_hours_lastday() {
        $q = $this->add('Model_Timesheet')->dsql();

        $q->where('T.user_id=u.id')
                ->field('sum(minutes)/60');
        $q->where('date<now() - interval 1 day');
        $q->where('date>now() - interval 2 day');
        return $q->select();
    }

    function calculate_hours_week() {
        return $this->add('Model_Timesheet')
                ->dsql()
                ->where('T.user_id=u.id')
                ->field('round(sum(minutes)/60)')
                //->where('date>now() - interval 1 week')
                ->where("YEARWEEK(date) = YEARWEEK(CURRENT_DATE)")
                ->select();
    }

    function calculate_hours_lastweek() {
        $q = $this->add('Model_Timesheet')->dsql();

        $q->where('T.user_id=u.id')
                ->field('round(sum(minutes)/60)');
        //  $q->where('date<now() - interval 1 week');
        $q->where(" YEARWEEK(date) = YEARWEEK(CURRENT_DATE - INTERVAL 7 DAY) ");

        return $q->select();
    }

    function calculate_hours_month() {
        return $this->add('Model_Timesheet')
                ->dsql()
                ->where('T.user_id=u.id')
                ->field('round(sum(minutes)/60)')
                ->where(' SUBSTRING(date FROM 1 FOR 7) =  SUBSTRING(CURRENT_DATE  FROM 1 FOR 7)')
                ->select();
    }

    function calculate_hours_lastmonth() {
        $q = $this->add('Model_Timesheet')->dsql();

        $q->where('T.user_id=u.id')
                ->field('round(sum(minutes)/60)');
//        SUBSTRING(ODate FROM 1 FOR 7) =
        //SUBSTRING(CURRENT_DATE - INTERVAL 1 MONTH FROM 1 FOR 7)


        $q->where(' SUBSTRING(date FROM 1 FOR 7) =  SUBSTRING(CURRENT_DATE - INTERVAL 1 MONTH FROM 1 FOR 7)');
        //$q->where('date>now() - interval 2 month');
        return $q->select();
    }

}
