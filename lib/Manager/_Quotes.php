<?php
class _Manager_Quotes extends View {
    public $quotes,$acceptance;
    function init(){
        parent::init();

        $this->api->stickyGET('id');
        $this->api->stickyGET($this->name);

        

        $v=$this->add('View')->setClass('left');
        
        $b=$v->add('Button')->set('Request For Quotation');
        $b->js('click', array(
        		$this->js()->univ()->redirect($this->api->url('manager/quotes/rfq'))
        ));
        
        $this->add('View')->setClass('clear');
        
        $cr=$this->add('CRUD', array(
            'grid_class'=>'Grid_Quotes',
            'allow_add'=>false,
            'role'=>'manager',
            'allowed_actions'=>array(
                'requirements',
                'estimation',
                'send_to_client',
                'approve',
            )
        ));
        $m=$this->add('Model_Quote')->notDeleted()->getThisOrganisation();
        $cr->setModel($m,
        		array('project_id','name','general_description','rate','currency','duration','deadline','status'),
        		array('project','user','name','estimated','estimpay','spent_time','rate','currency','durdead','status')
        		);
        if($cr->grid){
        	$cr->grid->addFormatter('status','status');
            $cr->grid->addPaginator(10);

//        	$cr->grid->addColumn('button','requirements');
//        	$cr->grid->addColumn('button','estimation','Request for estimate');
//        	$cr->grid->addColumn('button','send_to_client','Send Quote to the client');
//        	$cr->grid->addColumn('button','approve','Approve Estimation');
        }

        $this->add('P');
        
    }
}
