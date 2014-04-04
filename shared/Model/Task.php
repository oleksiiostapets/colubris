<?
class Model_Task extends Model_Task_Base {
    function init(){
        parent::init(); //$this->debug();
        $this->addQuoteName();
        $this->addGetConditions();

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

    function addGetConditions() {
        if ($_GET['project']) {
            $this->addCondition('project_id',$_GET['project']);
        }
        if ($_GET['quote']) {
            $this->addQuoteId();
            $this->addCondition('quote_id',$_GET['quote']);
        }
        if ($_GET['requirement']) {
            $this->addCondition('requirement_id',$_GET['requirement']);
        }
        if ($_GET['status']) {
            $this->addCondition('status',$_GET['status']);
        }
        if ($_GET['assigned']) {
            $this->addCondition('assigned_id',$_GET['assigned']);
        }
        return $this;
    }
    function addQuoteId() {
        $this->addExpression('quote_id',function($m,$q){
            $req = $m->add('Model_Requirement')->addCondition('id',$m->getElement('requirement_id'));
            $quote = $m->add('Model_Quote')->addCondition('id',$req->fieldQuery('quote_id'));
            return $quote->fieldQuery('id');
        });
    }
    function addQuoteName() {
        $this->addExpression('quote',function($m,$q){
            $req = $m->add('Model_Requirement')->addCondition('id',$m->getElement('requirement_id'));
            $quote = $m->add('Model_Quote')->addCondition('id',$req->fieldQuery('quote_id'));
            return $quote->fieldQuery('name');
        });
    }
}
