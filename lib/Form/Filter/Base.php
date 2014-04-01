<?php
/**
 * Created by Vadym Radvansky
 * Date: 3/27/14 3:01 PM
 */
class Form_Filter_Base extends Form {

    protected $project;
    protected $quote;

    function init() {
        parent::init();
        $this->addClass('atk-form-stacked horizontal');
        $this->addProject();
        $this->addQuote();
    }
    function addProject(){
        $mp = $this->add('Model_Project');
        $mp->forRole($this->app->getCurrentUserRole());

        $projects = $mp->getRows();
        $p_arr['0'] = 'all';
        foreach ($projects as $p){
            $p_arr[$p['id']]=$p['name'];
        }
        $this->project = $this->addField('DropDown','project');
        $this->project->setValueList($p_arr);

        // set value
        if ($g = $_GET['project']) {
            $this->project->set($g);
        }

        // reload on change
        $this->project->selectnemu_options = array(
            'change'=>$this->js(null,'
                function() {
                    $("#'.$this->name.'").submit();
                }')
        );
    }
    function addQuote(){
        if ($g = $_GET['project']) {
            $mq = $this->add('Model_Quote');
            $mq->addCondition('project_id',$g);
            $q_arr = $mq->getRows();
            $qn_arr['0'] = 'all';
            foreach ($q_arr as $q) {
                $qn_arr[$q['id']] = $q['name'];
            }
            $this->quote = $this->addField('DropDown','quote');
            $this->quote->setValueList($qn_arr);

            // set value
            if ($g = $_GET['quote']) {
                $this->quote->set($g);
            }

            // reload on change
            $this->quote->selectnemu_options = array(
                'change'=>$this->js(null,'
                    function() {
                        $("#'.$this->name.'").submit();
                    }')
            );
        }
    }
}