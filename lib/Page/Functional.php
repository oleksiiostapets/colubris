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
        $this->filter = $this->app->add('Controller_Filter');
        $filter_form = $this->add('Form_Filter_Base');
        $this->filter->setForm($filter_form);
        $this->filter->addViewToReload($filter_form);
    }
}