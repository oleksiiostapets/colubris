<?
class Model_Task extends Model_Task_Base {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('is_deleted',false);
    }
    function addDashCondition() {
        if (!$_GET['submit']) {
            $this->addCondition('status','<>','accepted');
        }
        $this->_dsql()->where(
            $this->_dsql()->orExpr()
                ->where('requester_id',$this->app->auth->model['id'])
                ->where('assigned_id',$this->app->auth->model['id'])
        );
        return $this;
    }

}
