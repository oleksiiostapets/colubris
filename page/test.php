<?php
class page_test extends Page {
    function init(){
        parent::init();

        $user = $this->app->currentUser();


        $m = $this->add('Model_Task')->prepareForSelect($user)->load(1634);

        var_dump($m->get());
        $this->add('View')->set($m->get());

    }
    /*function defaultTemplate(){
        return array('page/index');
    }*/
}
