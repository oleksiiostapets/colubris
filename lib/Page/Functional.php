<?php
/**
 * Created by Vadym Radvansky
 * Date: 4/11/14 3:53 PM
 */
class Page_Functional extends Page {
    protected $filter;
    function init() {
        parent::init();
    }

    protected function stickeGetFilterVars() {
        $this->app->stickyGet('project');
        $this->app->stickyGet('quote');
        $this->app->stickyGet('requirement');
        $this->app->stickyGet('status');
        $this->app->stickyGet('assigned');
    }

    protected function addFilter() {
		if ($_GET['task_id']){
			$task = $this->add('Model_Task')->debug()->load($_GET['task_id']);
			$r=$task->leftJoin('requirement','requirement_id','left','_r');
			$r->addField('quote_id','quote_id');

			var_dump($task->get());
			$_GET['project'] = $task->get('project_id');
			$_GET['requirement'] = $task->get('requirement_id');
		}

        $this->filter = $this->app->add('Controller_Filter');
        $filter_form = $this->add('Form_Filter_Base');
        $this->filter->setForm($filter_form);
        $this->filter->addViewToReload($filter_form);
    }
}