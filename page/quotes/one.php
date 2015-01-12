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
        $this->app->jquery->addStaticInclude('ng/vendor/angular.min');
        //$this->app->jquery->addStaticStylesheet('../js/ng/vendor/angular-xeditable/css/xeditable');
        $this->app->jquery->addStaticInclude('ng/vendor/angular-xeditable/js/xeditable.min');
        $this->app->jquery->addStaticInclude('ng/quotes/one/app');
        $this->app->jquery->addStaticInclude('ng/quotes/one/controllers/buttonSet');
        $this->app->jquery->addStaticInclude('ng/quotes/one/controllers/inlineCrud');
        $this->app->jquery->addStaticInclude('ng/quotes/one/directives/buttonSet');
        $this->app->jquery->addStaticInclude('ng/quotes/one/directives/inlineCrud');
        $this->app->jquery->addStaticInclude('ng/quotes/one/directives/requirementForm');
        $this->app->jquery->addStaticInclude('ng/quotes/one/directives/isIncluded');
//        $this->app->jquery->addStaticInclude('ng/quotes/one/services/Quote');
        $this->app->jquery->addStaticInclude('ng/_shared/directives/ngConfirmClick');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Project');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Quote');
        $this->app->jquery->addStaticInclude('ng/_shared/services/User');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Comment');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Task');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Requirement');
        $this->app->jquery->addStaticInclude('ng/_shared/services/API');

        $this->js(true)->colubris()->startRequirementApp(
            $this->id,
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
        return array('page/quotes/one');
    }
}