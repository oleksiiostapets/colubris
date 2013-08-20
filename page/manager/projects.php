<?
class page_manager_projects extends page_projectsfunctions {
    function page_index(){

        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Projects',
                    'url' => 'manager/projects',
                ),
            )
        ));

        $this->add('H2')->set('Projects');

        $cr=$this->add('CRUD');
        $cr->setModel('Project',
        		array('name','descr','client_id','demo_url','prod_url'),
        		array('name','descr','client','demo_url','prod_url')
        		);
        if($cr->grid){
			$cr->grid->addColumn('expander','participants');
			$cr->grid->addColumn('expander','tasks');
        }

    }
    function page_participants(){
        $this->api->stickyGET('project_id');
        $this->add('CRUD')->setModel($this->add('Model_Participant')
                ->addCondition('project_id',$_GET['project_id'])
                );
    }
}
