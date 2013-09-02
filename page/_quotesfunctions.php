<?php

class _page_quotesfunctions extends Page {
    function page_more(){
        if (!isset($_GET['requirement_id'])) {
            throw $this->exception('Provide $_GET[\'requirement_id\']');
        }
    	$this->api->stickyGET('requirement_id');
    	$req=$this->add('Model_Requirement')->load($_GET['requirement_id']);
    	
    	$this->add('View')->setHtml('<strong>Description:</strong> '.$this->api->colubris->makeUrls($req->get('descr')));

    	$this->add('View')->setHtml('<hr /><strong>Comments:</strong> ');
    	 
    	$cr=$this->add('CRUD', array('grid_class'=>'Grid_Reqcomments'));
    	 
    	$m=$this->add('Model_Reqcomment')
    			->addCondition('requirement_id',$_GET['requirement_id']);
    	$cr->setModel($m,
    			array('text','file_id'),
    			array('text','user','file','file_thumb','created_dts')
    	);
    	if($cr->grid){
    		$cr->add_button->setLabel('Add Comment');
    		$cr->grid->setFormatter('text','text');
    	}
    	if($_GET['delete']){
    		$comment=$this->add('Model_Reqcomment')->load($_GET['delete']);
    		$comment->delete();
    		$cr->js()->reload()->execute();
    	}
    }
        
}
