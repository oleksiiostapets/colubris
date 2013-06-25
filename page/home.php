<?php
class page_home extends Page {
	function init(){
		parent::init();

/*
        // Projects
        $grid=$this->add('Grid',null,'Projects');
        $m=$this->add('Model_Project')->setOrder('name');
        $grid->setModel($m,array('name','descr','url'));

    //    $grid->addButton('New Project? Request Our Quotation!');
     //   $grid->addButton('Help');

        $grid->addColumn('button','details_project','Details');
        if($_GET['details_project']){
            $this->js()->univ()->location(
                $this->api->getDestinationURL('project',
                array('project_id'=>$_GET['details_project'])
            ))->execute();
        }




        // TODO
        $grid=$this->add('Grid',null,'Todo');
        $grid ->setModel('Task',array('name','descr'));

        $grid->addColumn('button','details_todo');
 * 
 */
        /*
        $grid->addColumn('button','details_payment','Details');
        if($_GET['details_payment']){
            $this->js()->univ()->location(
                $this->api->getDestinationURL('payment',
                array('invoice_id'=>$_GET['details_payment'])
            ))->execute();
        }
         */


	}
    function defaultTemplate(){
        return array('page/home');
    }
}
