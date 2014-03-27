<?php
/**
 * Created by Vadym Radvansky
 * Date: 3/27/14 3:01 PM
 */
class Form_Filter_Base extends Form {
    function init() {
        parent::init();
        $this->addField('DropDown','project')->setValueList(array());
    }
}