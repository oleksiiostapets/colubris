<?php
class page_settings extends Page {
    use Helper_Url;
    protected $id;
    function init() {
        parent::init();
        $this->addNgJs();
    }
    protected function addNgJs() {
        $this->app->jquery->addStaticInclude('ng/vendor/angularjs');
        $this->app->jquery->addStaticInclude('ng/settings/app');
        $this->app->jquery->addStaticInclude('ng/settings/controllers/form');
//        $this->app->jquery->addStaticInclude('ng/settings/controllers/buttonSet');
//        $this->app->jquery->addStaticInclude('ng/settings/controllers/inlineCrud');
//        $this->app->jquery->addStaticInclude('ng/settings/directives/buttonSet');
//        $this->app->jquery->addStaticInclude('ng/settings/directives/inlineCrud');
//        $this->app->jquery->addStaticInclude('ng/settings/directives/requirementForm');
//        $this->app->jquery->addStaticInclude('ng/settings/services/Requirement');
//        $this->app->jquery->addStaticInclude('ng/settings/services/Comment');

        $this->js(true)->colubris()->startSettingsApp(
            1,
            $this->app->url('/'),
            $this->app->getConfig('url_prefix'),
            $this->app->getConfig('url_postfix')
        );
    }
    function defaultTemplate() {
        return array('page/settings');
    }
}