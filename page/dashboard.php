<?php

class page_dashboard extends Page {
    function page_index(){

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->api->currentUser()->canSeeDashboard() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }

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

        //$this->add('View_Switcher');
        $this->add('View_Dashboard');

        /*
        if ($this->api->currentUser()->isSystem()){
            $this->add('View_DashboardSystem');
        }
        */

    }
}
