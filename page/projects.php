<?php
class page_projects extends Page {
    use Helper_Url;
    function init() {
        parent::init();
        $this->addNgJs();
        $this->title = 'Projects';
        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Projects',
                    'url' => 'projects',
                ),
            )
        ),'bread_crumb');
    }
    protected function addNgJs() {
        $this->app->jquery->addStaticInclude('ng/vendor/angular.min');
        $this->app->jquery->addStaticInclude('ng/vendor/angular-route.min');
        $this->app->jquery->addStaticInclude('ng/vendor/angular-xeditable/js/xeditable.min');
        $this->app->jquery->addStaticInclude('ng/projects/app');
        $this->app->jquery->addStaticInclude('ng/projects/controllers/inlineCrud');
        $this->app->jquery->addStaticInclude('ng/projects/directives/inlineCrud');
        $this->app->jquery->addStaticInclude('ng/projects/directives/projectForm');
        $this->app->jquery->addStaticInclude('ng/_shared/directives/ngConfirmClick');
        $this->app->jquery->addStaticInclude('ng/_shared/services/API');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Project');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Quote');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Client');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Participant');
        $this->app->jquery->addStaticInclude('ng/_shared/services/User');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Right');

        $this->js(true)->colubris()->startProjectsApp(
            $this->app->url('/'),
            $this->app->getConfig('url_prefix'),
            $this->app->getConfig('url_postfix'),
            $this->app->url($this->app->getConfig('js_api_base_url')),
            $this->app->currentUser()->get('lhash'),
            $this->app->currentUser()->get('id'),
            $this->add('Model_User_Right')->getRights($this->app->currentUser()->get('id'))
        );
    }
    function defaultTemplate() {
        return array('page/projects');
    }
}