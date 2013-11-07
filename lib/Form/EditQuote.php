<?php
class Form_EditQuote extends Form {
    function init() {
        parent::init();
    }

    function setModel($model, $actual_fields = UNDEFINED){

        foreach($model->_dsql()->args['where'] as $k=>$v){
            if(!is_object($v)){
                if ($v[0]=='quote.status'){
                    unset($model->_dsql()->args['where'][$k]);
                }
            }
        }
        $model->getElement('status')->system(false)->editable(true);

        parent::setModel($model,$actual_fields);
        return $this->model;
    }

}