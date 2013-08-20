<?php
class Model_Organisation_Base extends Model_BaseTable {
    public $table='organisation';
    function init(){
        parent::init();
        $this->addField('name');
        $this->addField('desc')->type('text');
        $this->addField('is_deleted')->type('boolean')->defaultValue('0');
    }
}