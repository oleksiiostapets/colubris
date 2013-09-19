<?
class Model_Task extends Model_Task_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_deleted',false);
    }

    function whatTaskFieldsUserCanEdit($user) {
        if ($user->isAdmin()) {
            return array();
        } else if ($user->isManager()) {
            return array('name','descr_original','priority','type','status','estimate','requester_id','assigned_id');
        } else if ($user->isDeveloper()) {
            return array('name','descr_original','priority','type','status','estimate','requester_id','assigned_id');
        } else if ($user->isClient()) {
            return array('name','descr_original','priority','type','status','requester_id','assigned_id');
        }
        throw $this->exception('Wrong role');
    }

    function whatTaskFieldsUserCanSee($user) {
        if ($user->isAdmin()) {
            return array();
        } else if ($user->isManager()) {
            return array('name','priority','type','status','estimate','spent_time','requester','assigned');
        } else if ($user->isDeveloper()) {
            return array('name','priority','type','status','estimate','spent_time','requester','assigned');
        } else if ($user->isClient()) {
            return array('name','priority','type','status','estimate','requester','assigned');
        }
        throw $this->exception('Wrong role');
    }
}
