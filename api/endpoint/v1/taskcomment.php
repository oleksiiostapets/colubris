<?php
class endpoint_v1_taskcomment extends Endpoint_v1_General {

    public $model_class = 'Taskcomment';

    function init() {
        parent::init();
        var_dump($_GET);
        echo '<hr>';
    }

}