<?php
class page_account extends Page {
    use Helper_Url;
    protected $id;
    function init() {
        parent::init();
        $this->addNgJs();
        $this->title = 'Settings';
        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Settings',
                    'url' => 'account',
                ),
            )
        ),'bread_crumb');
    }
    protected function addNgJs() {
        $this->app->jquery->addStaticInclude('ng/vendor/angularjs');
        $this->app->jquery->addStaticInclude('ng/settings/app');
        $this->app->jquery->addStaticInclude('ng/settings/controllers/forms');
        $this->app->jquery->addStaticInclude('ng/settings/directives/forms');
        $this->app->jquery->addStaticInclude('ng/_shared/services/API');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Setting');

        $this->js(true)->colubris()->startSettingsApp(
            $this->app->currentUser()->get('id'),
            $this->app->url('/'),
            $this->app->getConfig('url_prefix'),
            $this->app->getConfig('url_postfix'),
            $this->app->url($this->app->getConfig('js_api_base_url')),
            $this->app->currentUser()->get('lhash')
        );
    }
    function defaultTemplate() {
        return array('page/account');
    }
}