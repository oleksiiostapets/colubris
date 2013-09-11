<?php
class Form_EditTask extends Form {
    function init() {
        parent::init();
        //$m=$this->getModel();
        //$m=$this->add('Model_Task');
        //$this->setModel($m,array('name','descr_original','estimate','priority','type','status','requester_id','assigned_id'));
    }

    function setModel($model, $actual_fields = UNDEFINED){
        foreach($model->_dsql()->args['where'] as $k=>$v){
            if(!is_object($v)){
                if ($v[0]=='task.status'){
                    unset($model->_dsql()->args['where'][$k]);
                }
            }
        }
        echo"<pre>";
        var_dump($model->_dsql()->stmt);
        echo"</pre>";
//        var_dump($model->_dsql()->args['where']);
//        echo "<hr>";
 //       exit;
        parent::setModel($model,$actual_fields);
        return $this->model;
    }

}