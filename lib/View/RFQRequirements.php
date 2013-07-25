<?php
class View_RFQRequirements extends View {
    function init(){
        parent::init();

        $cr = $this->add('CRUD',array('allow_add'=>$this->allow_add,'allow_edit'=>$this->allow_edit,'allow_del'=>$this->allow_del));
      	$cr->setModel($this->requirements,
       		array('name','descr','estimate','file_id'),
       		array('name','estimate','spent_time','file','user','count_comments')
       		);
        
        if($cr->grid){
        	$cr->grid->addColumn('expander','details');
        	$cr->grid->addColumn('expander','comments');
        	$cr->grid->addFormatter('file','download');
        }
    }
}
