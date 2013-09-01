<?
class page_client_projects extends page_projectsfunctions {
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
                    'name' => 'Projects',
                    'url' => 'client/projects',
                ),
            )
        ));

        $this->add('H2')->set('Projects');

        $m = $this->add('Model_Project');
        if ($this->api->currentUser()->isClient()) $m->forClient();

        $cr=$this->add('CRUD',array('allow_del'=>false,'allow_edit'=>false,'allow_add'=>false));
        $cr->setModel($m,array('name','descr','client','demo_url','prod_url'));
        if($cr->grid){
            $cr->grid->addPaginator(10);
			$cr->grid->addColumn('expander','tasks');
        }

    }
}
