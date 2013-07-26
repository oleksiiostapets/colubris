<?php
class View_RFQRequirements extends View {
    function init(){
        parent::init();

        if(!isset($this->edit_fields)) $this->edit_fields=array('name','descr','estimate','file_id');
        $cr = $this->add('CRUD',array('allow_add'=>$this->allow_add,'allow_edit'=>$this->allow_edit,'allow_del'=>$this->allow_del));
      	$cr->setModel($this->requirements,
       		$this->edit_fields,
       		array('name','estimate','spent_time','file','user','count_comments')
       		);
        
        if($cr->grid){
        	$this->api->memorize('number',0);
        	$cr->grid->addColumn('inline','number');
        	$cr->grid->addFormatter('number','number');
        	$cr->grid->addColumn('expander','details');
        	$cr->grid->addColumn('expander','comments');
        	$cr->grid->addFormatter('file','download');
        	$cr->grid->addFormatter('estimate','estimate');
        	$cr->grid->setFormatter('name','text');
        	$cr->grid->addOrder()->move('number','first')->now();
        }
    }
}
