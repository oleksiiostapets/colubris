<?
class page_client_projects extends Page {
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
			$cr->grid->addColumn('expander','tasks');
        }

    }
    function page_tasks(){
        $this->api->stickyGET('project_id');
        $cr=$this->add('CRUD',array('grid_class'=>'Grid_Tasks'));
        $m=$this->add('Model_Task')
                ->addCondition('project_id',$_GET['project_id']);
        $cr->setModel($m,
        		array('name','descr_original','priority','status','estimate','spent_time','assigned_id'),
        		array('name','descr_original','priority','status','estimate','spent_time','assigned')
        		);
        if($cr->grid){
        	$cr->grid->addFormatter('status','status');
        }
    }
}
