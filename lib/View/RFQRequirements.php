<?php
class View_RFQRequirements extends View {
    function init(){
        parent::init();

        if(!isset($this->edit_fields)) $this->edit_fields=array('name','descr','estimate','file_id');
        $cr = $this->add('CRUD',
            array(
                'allow_add'=>$this->allow_add,'allow_edit'=>$this->allow_edit,'allow_del'=>$this->allow_del,
                'grid_class'=>'Grid_Requirements'
            )
        );
      	$cr->setModel($this->requirements,
       		$this->edit_fields,
       		array('name','estimate','spent_time','file','user','count_comments')
        );
        
        if($cr->grid){
        	$cr->grid->addColumn('expander','more');
        	//$cr->grid->addFormatter('file','download');
        	//$cr->grid->addFormatter('estimate','estimate');
        	$cr->grid->setFormatter('name','wrap');
        }
    }
}

class Grid_Requirements extends Grid_CountLines {
    function init() {
        parent::init();
    }
    function setModel($model, $actual_fields = UNDEFINED) {
        parent::setModel($model, $actual_fields);
        if ($this->hasColumn('count_comments')) {
            $this->getColumn('count_comments')->setCaption('Comm.');
        }
    }
    function setCaption($name) {
        $this->columns[$this->last_column]['descr'] = $name;
        return $this;
    }
    function formatRow() {
        parent::formatRow();
    }
}