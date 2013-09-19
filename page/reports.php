<?php
class page_reports extends Page {
    function page_index(){

        // Checking client's read permission to this quote and redirect to denied if required
        //if( !$this->api->currentUser()->canUserMenageClients() ){
        //    throw $this->exception('You cannot see this page','Exception_Denied');
        //}

        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Reports',
                    'url' => 'reports',
                ),
            )
        ));

        $this->add('View_ReportsSwitcher');
        $this->add('View_Report',array('grid_show_fields'=>$this->getGridFields()));

    }
    function getGridFields() {
        if ($this->api->currentUser()->canBeClient()) {
            return array('project','quote','name','status','type','estimate','spent','date');
        }
        if ($this->api->currentUser()->canBeDeveloper()) {
            return array('project','quote','name','status','type','estimate','spent','date','performer');
        }
        if ($this->api->currentUser()->canBeManager()) {
            return array('project','quote','name','status','type','estimate','spent','date','performer');
        }
    }
}