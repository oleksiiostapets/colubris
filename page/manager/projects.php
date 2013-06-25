<?
class page_manager_projects extends Page {
    function page_index(){
        $cr=$this->add('CRUD');
        $cr->setModel('Project',array('name','descr','client','demo_url','prod_url'));
        if($cr->grid){
			$cr->grid->addColumn('expander','participants');
        }

    }
    function page_participants(){
        $this->api->stickyGET('project_id');
        $this->add('CRUD')->setModel($this->add('Model_Participant')
                ->addCondition('project_id',$_GET['project_id'])
                );
    }
}
