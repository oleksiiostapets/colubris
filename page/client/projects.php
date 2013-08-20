<?
class page_client_projects extends page_projectsfunctions {
    function page_index(){

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

        $cr=$this->add('CRUD',array('allow_del'=>false,'allow_edit'=>false,'allow_add'=>false));
        $cr->setModel('Project',array('name','descr','client','demo_url','prod_url'));
        if($cr->grid){
            $cr->grid->addPaginator(10);
			$cr->grid->addColumn('expander','tasks');
        }

    }
}
