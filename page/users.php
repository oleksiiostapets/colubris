<?php
class page_users extends Page {
    use Helper_Url;
    function init() {
        parent::init();
        $this->addNgJs();
        $this->title = 'Users';
        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Users',
                    'url' => 'users',
                ),
            )
        ),'bread_crumb');
    }
    protected function addNgJs() {
        $this->app->jquery->addStaticInclude('ng/vendor/angularjs');
        $this->app->jquery->addStaticInclude('ng/users/app');
        $this->app->jquery->addStaticInclude('ng/users/controllers/inlineCrud');
        $this->app->jquery->addStaticInclude('ng/users/directives/inlineCrud');
        $this->app->jquery->addStaticInclude('ng/users/directives/userForm');
        $this->app->jquery->addStaticInclude('ng/_shared/services/API');
        $this->app->jquery->addStaticInclude('ng/_shared/services/User');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Right');
        $this->app->jquery->addStaticInclude('ng/users/directives/isChecked');
//        $this->app->jquery->addStaticInclude('ng/_shared/services/Quote');

        $this->js(true)->colubris()->startUsersApp(
            $this->app->url('/'),
            $this->app->getConfig('url_prefix'),
            $this->app->getConfig('url_postfix'),
            $this->app->url($this->app->getConfig('js_api_base_url')),
            $this->app->currentUser()->get('lhash')
        );
    }
    function defaultTemplate() {
        return array('page/users');
    }
}
