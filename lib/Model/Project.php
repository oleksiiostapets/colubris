<?
class Model_Project extends Model_Project_Base {
    function init(){
        parent::init();
        $this->addCondition('is_deleted',false);
    }



    /* **********************************
     *
     *      PROJECT ACCESS RULES
     *
     */
    function hasUserReadAccess($user) {
    }

    function hasUserEstimateAccess($user) {
    }

    function hasUserDeleteAccess($user) {
    }

    function hasUserEditAccess($user) {
    }
}
