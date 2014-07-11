<?php
/**
 * Created by Vadym Radvansky
 * Date: 7/8/14 12:10 PM
 */
class page_quotes_one extends Page {
    use Helper_Url;
    protected $id;
    function init() {
        parent::init();
        $this->id = $this->checkGetParameter('id');
        $this->addNgJs();
    }
    protected function addNgJs() {
        $this->app->jquery->addStaticInclude('ng/vendor/angularjs');
        $this->app->jquery->addStaticInclude('ng/quotes/one/app');
        $this->app->jquery->addStaticInclude('ng/quotes/one/controllers/buttonSet');
        $this->app->jquery->addStaticInclude('ng/quotes/one/controllers/inlineCrud');
        $this->app->jquery->addStaticInclude('ng/quotes/one/directives/buttonSet');
        $this->app->jquery->addStaticInclude('ng/quotes/one/directives/inlineCrud');
        $this->app->jquery->addStaticInclude('ng/quotes/one/directives/requirementForm');
        $this->app->jquery->addStaticInclude('ng/quotes/one/services/Requirement');
        $this->app->jquery->addStaticInclude('ng/quotes/one/services/Comment');

        $this->js(true)->colubris()->startRequirementApp(
            $this->id,
            $this->app->url('/'),
            $this->app->getConfig('url_prefix'),
            $this->app->getConfig('url_postfix')
        );
    }
    function defaultTemplate() {
        return array('page/quotes/one');
    }
}