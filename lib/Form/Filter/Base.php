<?php
/**
 * Created by Vadym Radvansky
 * Date: 3/27/14 3:01 PM
 */
class Form_Filter_Base extends Form {

    protected $project     = false;
    protected $quote       = false;
    protected $requirement = false;

    function init() {
        parent::init();
        $this->addClass('atk-form-stacked horizontal');
        $this->addProject();
        $this->addQuote();
        $this->addRequirement();
        $this->addStatus();
        $this->addAssigned();
    }
    function addProject(){
        $project_model = $this->add('Model_Project');
        $project_model->forRole($this->app->getCurrentUserRole());

        $projects = $project_model->getRows();
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
            'change' => $this->js(null,'
                function() {'.
                    $this->js()->colubris()->reloadForm($this->name,'project')
                .'}'
            )
        );
    }
    function addQuote(){
        if ($g = $_GET['project']) {
            $quote_model = $this->add('Model_Quote');
            $quote_model->addCondition('project_id',$g);
            $q_arr = $quote_model->getRows();
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
                'change' => $this->js(null,'
                    function() {'.
                        $this->js()->colubris()->reloadForm($this->name,'quote')
                    .'}'
                )
            );
        }
    }
    function addRequirement(){
        if ($_GET['project'] && $g = $_GET['quote']) {
            $requirement_model = $this->add('Model_Requirement');
            $requirement_model->addCondition('quote_id',$g);
            $r_arr = $requirement_model->getRows();
            $rn_arr['0'] = 'all';
            foreach($r_arr as $r){
                $rn_arr[$r['id']] = $r['name'];
            }
            $this->requirement = $this->addField('DropDown','requirement');
            $this->requirement->setValueList($rn_arr);

            // set value
            if ($g = $_GET['requirement']) {
                $this->requirement->set($g);
            }

            // reload on change
            $this->requirement->selectnemu_options = array(
                'change' => $this->js(null,'
                    function() {'.
                        $this->js()->colubris()->reloadForm($this->name,'requirement')
                    .'}'
                )
            );

        }
    }
    function addStatus() {
        $s_arr = array_merge(array(''=>'all'),$this->app->task_statuses);
        $this->status = $this->addField('DropDown','status');
        $this->status->setValueList($s_arr);

        // set value
        if ($g = $_GET['status']) {
            $this->status->set($g);
        }

        // reload on change
        $this->status->selectnemu_options = array(
            'change' => $this->js(null,'
                function() {'.
                    $this->js()->colubris()->reloadForm($this->name,'status')
                .'}'
            )
        );
    }
    function addAssigned() {
        $assigned_model = $this->add('Model_User_Task')->setOrder('name');
        $a_arr = $assigned_model->getRows();
        $u_arr['0'] = 'all';
        foreach($a_arr as $a){
            $u_arr[$a['id']]=$a['name'];
        }
        $this->assigned = $this->addField('DropDown','assigned');
        $this->assigned->setValueList($u_arr);
    }
}