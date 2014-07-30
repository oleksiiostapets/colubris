<?php
/**
 * Created by Vadym Radvansky
 * Date: 7/8/14 12:10 PM
 */
class page_clients2 extends Page {
    use Helper_Url;
    function init() {
        parent::init();
        $this->addNgJs();
    }
    protected function addNgJs() {
        $this->app->jquery->addStaticInclude('ng/vendor/angularjs');
        $this->app->jquery->addStaticInclude('ng/clients/app');
        $this->app->jquery->addStaticInclude('ng/clients/controllers/inlineCrud');
        $this->app->jquery->addStaticInclude('ng/clients/directives/inlineCrud');
        $this->app->jquery->addStaticInclude('ng/clients/directives/clientForm');
        $this->app->jquery->addStaticInclude('ng/_shared/services/API');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Client');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Project');

        $this->js(true)->colubris()->startClientsApp(
            $this->app->url('/'),
            $this->app->getConfig('url_prefix'),
            $this->app->getConfig('url_postfix')
        );
    }
    function defaultTemplate() {
        return array('page/clients');
    }
}