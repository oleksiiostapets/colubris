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
        $this->title = 'Tasks';
        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Tasks',
                    'url' => 'tasks',
                ),
            )
        ),'bread_crumb');
    }
    protected function addNgJs() {
        $this->app->jquery->addStaticInclude('ng/vendor/angularjs');
        $this->app->jquery->addStaticInclude('ng/vendor/angular-xeditable/js/xeditable.min');
        $this->app->jquery->addStaticInclude('ng/tasks/app');
        $this->app->jquery->addStaticInclude('ng/tasks/controllers/inlineCrud');
        $this->app->jquery->addStaticInclude('ng/tasks/directives/inlineCrud');
        $this->app->jquery->addStaticInclude('ng/tasks/directives/taskForm');
        $this->app->jquery->addStaticInclude('ng/_shared/directives/ngConfirmClick');
        $this->app->jquery->addStaticInclude('ng/_shared/services/API');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Project');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Quote');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Requirement');
        $this->app->jquery->addStaticInclude('ng/_shared/services/User');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Task');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Time');
        $this->app->jquery->addStaticInclude('ng/_shared/directives/filter');

        $this->js(true)->colubris()->startTasksApp(
            $this->app->url('/'),
            $this->app->getConfig('url_prefix'),
            $this->app->getConfig('url_postfix'),
            $this->app->url($this->app->getConfig('js_api_base_url')),
            $this->app->currentUser()->get('lhash')
        );
    }
    function defaultTemplate() {
        return array('page/tasks');
    }
}