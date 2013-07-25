<?php

class page_quotesfunctions extends Page {
    function page_details(){
    	$this->api->stickyGET('requirement_id');
    	$req=$this->add('Model_Requirement')->load($_GET['requirement_id']);
    	
    	$this->add('View')->setHtml('<strong>Description:</strong> '.$this->api->makeUrls($req->get('descr')));
    }
    function page_comments(){
    	$this->api->stickyGET('requirement_id');
    	$cr=$this->add('CRUD', array('grid_class'=>'Grid_Reqcomments'));
    	 
    	$m=$this->add('Model_Reqcomment')
    			->addCondition('requirement_id',$_GET['requirement_id']);
    	$cr->setModel($m,
    			array('text'),
    			array('text','user')
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
