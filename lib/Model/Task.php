<?
class Model_Task extends Model_Task_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_deleted',false);
    }

    function whatTaskFieldsUserCanEdit($user) {
        if ($user->isCurrentUserAdmin()) {
            return array();
        } else if ($user->isCurrentUserManager()) {
            return array('name','descr_original','priority','type','status','estimate','requester_id','assigned_id');
        } else if ($user->isCurrentUserDev()) {
            return array('name','descr_original','priority','type','status','estimate','requester_id','assigned_id');
        } else if ($user->isCurrentUserClient()) {
            return array('name','descr_original','priority','type','status','requester_id','assigned_id');
        }
        throw $this->exception('Wrong role');
    }

    function whatTaskFieldsUserCanSee($user) {
        if ($user->isCurrentUserAdmin()) {
            return array();
        } else if ($user->isCurrentUserManager()) {
            return array('name','priority','type','status','estimate','spent_time','requester','assigned');
        } else if ($user->isCurrentUserDev()) {
            return array('name','priority','type','status','estimate','spent_time','requester','assigned');
        } else if ($user->isCurrentUserClient()) {
            return array('name','priority','type','status','estimate','spent_time','requester','assigned');
        }
        throw $this->exception('Wrong role');
    }
}
