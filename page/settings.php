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
        $this->app->jquery->addStaticInclude('ng/settings/controllers/forms');
        $this->app->jquery->addStaticInclude('ng/settings/directives/forms');
        $this->app->jquery->addStaticInclude('ng/settings/services/Settings');

        $this->js(true)->colubris()->startSettingsApp(
            $this->app->currentUser()->get('id'),
            $this->app->url('/'),
            $this->app->getConfig('url_prefix'),
            $this->app->getConfig('url_postfix')
        );
    }
    function defaultTemplate() {
        return array('page/settings');
    }
}