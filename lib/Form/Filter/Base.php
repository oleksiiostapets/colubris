<?php
/**
 * Created by Vadym Radvansky
 * Date: 3/27/14 3:01 PM
 */
class Form_Filter_Base extends Form {

    protected $project;

    function init() {
        parent::init();
        $this->addClass('atk-form-stacked horizontal');
        $this->addProject();
    }
    function addProject(){
        $mp = $this->add('Model_Project');
        $mp->forRole($this->app->getCurrentUserRole());

        $projects = $mp->getRows();
        $p_arr = array();
        foreach ($projects as $p){
            $p_arr[$p['id']]=$p['name'];
        }
        $this->project = $this->addField('dropdown','project');
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
}