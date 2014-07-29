<?php
/**
 * Created by Vadym Radvansky
 * Date: 7/8/14 12:10 PM
 */
class page_tasks extends Page {
    use Helper_Url;
    function init() {
        parent::init();
        $this->addNgJs();
    }
    protected function addNgJs() {
        $this->app->jquery->addStaticInclude('ng/vendor/angularjs');
        $this->app->jquery->addStaticInclude('ng/tasks/app');
        $this->app->jquery->addStaticInclude('ng/tasks/controllers/inlineCrud');
        $this->app->jquery->addStaticInclude('ng/tasks/directives/inlineCrud');
        $this->app->jquery->addStaticInclude('ng/_shared/services/API');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Project');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Quote');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Requirement');
        $this->app->jquery->addStaticInclude('ng/_shared/services/User');
        $this->app->jquery->addStaticInclude('ng/_shared/services/TaskStatus');
        $this->app->jquery->addStaticInclude('ng/_shared/directives/filter');
        $this->app->jquery->addStaticInclude('ng/tasks/services/Task');

        $this->js(true)->colubris()->startTasksApp(
            $this->app->url('/'),
            $this->app->getConfig('url_prefix'),
            $this->app->getConfig('url_postfix')
        );
    }
    function defaultTemplate() {
        return array('page/tasks');
    }
}