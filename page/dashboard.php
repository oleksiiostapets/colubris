<?php

class page_dashboard extends Page {
    function page_index(){

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->app->model_user_rights->canSeeDashboard() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }

        $this->addBC();
        $this->add('View_Dashboard');
        $this->addSystemDash();

    }
    private function addBC() {
        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Dashboard',
                    'url' => 'dashboard',
                ),
            )
        ));
    }
    private function addSystemDash() {
        /*
        if ($this->api->currentUser()->isSystem()){
            $this->add('View_DashboardSystem');
        }
        */
    }
}
