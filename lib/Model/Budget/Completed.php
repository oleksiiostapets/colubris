<?php
/**
  * This indicates a budget, which functionality was completed, but it's not accepted yet
  */
class Model_Budget_Completed extends Model_Budget {
    function init(){
        parent::init();

        $this->addCondition('state','bugfixes');

        $this->addField('bugs')->calculated(true);
        $this->addField('tasks')->calculated(true);
    }
    function calculate_bugs(){
        $st=$this->add('Model_Task_Bug')->dsql()
            ->where('tsk.budget_id=bu.id')
            ->field('count(*)')
            ->select();
        return "concat(($st),' bugs')";
    }
    function calculate_tasks(){
        $st=$this->add('Model_Task_Enhancement')->dsql()
            ->where('tsk.budget_id=bu.id')
            ->field('count(*)')
            ->select();
        return "concat(($st),' tasks')";
    }
}
