<?php
class Model_Budget extends Model_Quote {
    function init(){
        parent::init();
		$this->notDeleted()->getThisOrganisation();
        $this->addCondition('status','estimation_approved');
    }
}
