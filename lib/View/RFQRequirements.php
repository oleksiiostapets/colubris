<?php
class View_RFQRequirements extends View {
    public $quote;
    public $total_view;
    function init(){
        parent::init();
        // $this->quote must be setted
        if (is_null($this->quote)) {
            throw $this->exception('Set $this->quote while adding.');
        }

        if(!isset($this->edit_fields)) $this->edit_fields=array('name','descr','estimate','file_id');
        $cr = $this->add('CRUD',
            array(
                'allow_add'=>$this->allow_add,'allow_edit'=>$this->allow_edit,'allow_del'=>$this->allow_del,
                'grid_class'=>'Grid_Requirements','quote'=>$this->quote,'total_view'=>$this->total_view
            )
        );
      	$cr->setModel($this->requirements,
       		$this->edit_fields,
       		array('is_included','name','estimate','spent_time','file','user','count_comments')
        );
        
        if($cr->grid){
        	$cr->grid->addColumn('expander','more');
        	//$cr->grid->addFormatter('file','download');
        	//$cr->grid->addFormatter('estimate','estimate');
        	$cr->grid->setFormatter('name','wrap');
        }
    }
}