<?php
/**
 * Created by Vadym Radvansky
 * Date: 7/8/14 12:10 PM
 */
class page_clients extends Page {
    use Helper_Url;
    function init() {
        parent::init();
        $this->addNgJs();

        $this->title = 'Clients';

        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Clients',
                    'url' => 'clients',
                ),
            )
        ),'bread_crumb');
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
        $this->app->jquery->addStaticInclude('ng/_shared/services/Quote');

        $this->js(true)->colubris()->startClientsApp(
            $this->app->url('/'),
            $this->app->getConfig('url_prefix'),
            $this->app->getConfig('url_postfix'),
            $this->app->url($this->app->getConfig('js_api_base_url')),
            $this->app->currentUser()->get('lhash')
        );
    }
    function defaultTemplate() {
        return array('page/clients');
    }
}