<?php
class page_users extends Page {
    use Helper_Url;
    function init() {
        parent::init();
        $this->addNgJs();
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
//        $this->app->jquery->addStaticInclude('ng/_shared/services/Project');
//        $this->app->jquery->addStaticInclude('ng/_shared/services/Quote');

        $this->js(true)->colubris()->startUsersApp(
            $this->app->url('/'),
            $this->app->getConfig('url_prefix'),
            $this->app->getConfig('url_postfix')
        );
    }
    function defaultTemplate() {
        return array('page/users');
    }
}
