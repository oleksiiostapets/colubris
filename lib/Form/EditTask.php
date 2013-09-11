<?php
class Form_EditTask extends Form {
    function init() {
        parent::init();
    }

    function setModel($model, $actual_fields = UNDEFINED){
        foreach($model->_dsql()->args['where'] as $k=>$v){
            if(!is_object($v)){
                if ($v[0]=='task.status'){
                    unset($model->_dsql()->args['where'][$k]);
                }
                if ($v[0]==$model->table.'.status' || $v[0]==$model->table.'.assigned_id'){ // assigned_id
                    unset($model->_dsql()->args['where'][$k]);
                }
            }
        }
        $model->getElement('status')->system(false)->editable(true);
        $model->getElement('assigned_id')->system(false)->editable(true);

        parent::setModel($model,$actual_fields);
        return $this->model;
    }

}