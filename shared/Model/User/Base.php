<?php
class Model_User_Base extends Model_BaseTable {
    public $table='user';
    function init(){
        parent::init(); //$this->debug();
        if (@$this->app->auth) $this->app->auth->addEncryptionHook($this);

        // fields
        $this->addField('email')->mandatory('required');
        $this->addField('name');
        $this->addField('password')->display(array('form'=>'password'))->mandatory('required');
        $this->addField('is_admin')->type('boolean');
        $this->addField('is_manager')->type('boolean');
        $this->addField('is_financial')->type('boolean')->caption('Is Financial Manager');
        $this->addField('is_developer')->type('boolean')->caption('Is Team Member');
        $this->addField('is_sales')->type('boolean')->caption('Is Sales Manager');
//        $this->addField('is_timereport')->type('boolean')->caption('Is Time Reports');
        $this->addField('hash');
        $this->addField('mail_task_changes')->type('boolean')->caption('Send when task changed');
        $this->addField('is_deleted')->type('boolean')->defaultValue('0');
        //$this->addField('deleted_id')->refModel('Model_User');
        $this->hasOne('User','deleted_id');
        $this->addHook('beforeDelete', function($m){
            $m['deleted_id']=$m->app->currentUser()->get('id');
        });

        $this->addField('is_system')->defaultValue('0')->type('boolean');
        $this->hasOne('Client');
        //$this->addField('client_id')->refModel('Model_Client');

        $this->addField('chash');

        $this->hasOne('Organisation')->mandatory('required');

        // expressions
        $this->addExpression('is_client')->datatype('boolean')->set(function($m,$q){
            return $q->dsql()
                    ->expr('if(client_id is null,false,true)');
        });

        // order
        $this->setOrder('name');


        // hooks
        $this->addHook('beforeInsert',function($m){
            if($m->getBy('email',$m['email'])) throw $m
                    ->exception('User with this email already exists','ValidityCheck')
                    ->setField('email');
        });

        $this->addHook('beforeModify',function($m){
            if($m->dirty['email']) throw $m
                ->exception('Do not change email for existing user','ValidityCheck')
                ->setField('email');
        });
    }
    function me(){
        $this->addCondition('id',$this->app->auth->get('id'));
        return $this;
    }
    function beforeInsert(&$d){
        $d['hash']=md5(uniqid());
        return parent::beforeInsert($d);
    }
    function resetPassword(){
        throw $this->exception('Function resetPassword is not implemented yet');
    }



    /* **********************************
     *
     *      GET RELATED MODELS
     *
     */
    function getDashboardCommentsModel($type) {
        $type_low = strtolower($type);
        if ($this->isClient()) {
            $m = $this->add('Model_'.$type.'comment_Client');
        } elseif ($this->app->currentUser()->isDeveloper()) {
            $m = $this->add('Model_'.$type.'comment_Developer');
        } else {
            $m = $this->add('Model_'.$type.'comment');
        }
        //$m->debug();
        $m->addCondition('user_id','<>',$this['id']);
        $m->setOrder('created_dts',true);

        $proxy_check = $this->add('Model_'.$type.'commentUser');
        $proxy_check->addCondition('user_id',$this['id']);
        $proxy_check->_dsql()->field($type_low.'comment_id');
        $m->addCondition('id','NOT IN',$proxy_check->_dsql());

        switch ($type) {
            case 'Req':
                $jr = $m->join('requirement.id','requirement_id','left','_req');
                $jr->addField('requirement_name','name');
                $jr->addField('quote_id','quote_id');
                //$jr->addField('requirement_id','id');

                $jq = $jr->join('quote.id','quote_id','left','_quote');
                $jq->addField('quote_name','name');
                $jq->addField('quote_status','status');

                $jp = $jq->join('project.id','project_id','left','_pr');
                $jp->addField('project_name','name');
                $jp->addField('organisation_id','organisation_id');
                $m->addCondition('organisation_id',$this['organisation_id']);

                $m->addCondition('quote_status','IN',array('quotation_requested','estimate_needed','not_estimated','estimated'));
                break;
            case 'Task':
                $jt = $m->join('task.id','task_id','left','_t');
                $jt->addField('task_name','name');

                $jp = $jt->join('project.id','project_id','left','_pr');
                $jp->addField('project_name','name');
                $jp->addField('organisation_id','organisation_id');
                $m->addCondition('organisation_id',$this['organisation_id']);
                break;
            default:
                throw $this->exception('There is no such a type: '.$type);
        }
        return $m;
    }



    /* *********************************
     *
     *             GET ROLES
     *
     */
    function canBeAdmin() {
        return ($this['is_admin']?true:false);
    }
    function canBeDeveloper() {
        return ($this['is_developer']?true:false);
    }
    function canBeClient() {
        return ($this['is_client']?true:false);
    }
    function canBeManager() {
        return ($this['is_manager']?true:false);
    }
    function canBeSales() {
        return ($this['is_sales']?true:false);
    }
    function canBeSystem() {
        return ($this['is_system']?true:false);
    }


    function canSeeFinance() { // canSeeFinance
        return ($this['is_financial']?true:false);
    }



    /* **********************************
     *
     *          CURRENT USER ROLE
     *
     */
    function isSystem() {
        return ($this->app->getCurrentUserRole() == 'system');
    }
    function isAdmin() {
        return ($this->app->getCurrentUserRole() == 'admin');
    }
    function isFinancial() {
        return ($this->app->auth->model['is_financial']);
    }
    function isManager() {
        return ($this->app->getCurrentUserRole() == 'manager');
    }
    function isSales() {
        return ($this->app->getCurrentUserRole() == 'sales');
    }
    function isDeveloper() {
        return ($this->app->getCurrentUserRole() == 'developer');
    }
    function isClient() {
        return ($this->app->getCurrentUserRole() == 'client');
    }



    /* **********************************
     *
     *      PROJECT ACCESS RULES
     *
     */
    function canSeeProject($project) {
    }
    function canCreateProject() {
        return $this->checkRoleSimpleRights(array(false,true,false,true,false));
    }
    function canDeleteProject() {
        return $this->checkRoleSimpleRights(array(false,true,false,false,false));
    }
    function canEditProject() {
        return $this->checkRoleSimpleRights(array(false,true,false,false,false));
    }
    function canSeeProjectParticipantes() {
        return $this->checkRoleSimpleRights(array(false,true,false,false,false));
    }
    function canSeeProjectTasks() {
        return $this->checkRoleSimpleRights(array(true,true,true,true,false));
    }
    function getProjectFormFields() {
        if ($this->isAdmin()) {
            return false;
        } else if ($this->isManager()) {
            return array('name','descr','client_id','demo_url','prod_url','repository');
        } else if ($this->isDeveloper()) {
            return array('name','descr','client_id','demo_url','prod_url');
        } else if ($this->isClient()) {
            return array('name','descr');
        } else if ($this->isSystem()) {
            return false;
        }
        throw $this->exception('Wrong role');
    }
    function getProjectGridFields() {
        if ($this->isAdmin()) {
            return false;
        } else if ($this->isManager()) {
            return array('name','descr','client','demo_url','prod_url','repository');
        } else if ($this->isDeveloper()) {
            return array('name','descr','client','demo_url','prod_url','repository');
        } else if ($this->isClient()) {
            return array('name','descr','client','demo_url','prod_url');
        } else if ($this->isSystem()) {
            return false;
        }
        throw $this->exception('Wrong role');
    }



    /* **********************************
     *
     *      DASHBOARD ACCESS RULES
     *
     */
    function canSeeDashboard() {
        return $this->checkRoleSimpleRights(array(true,true,true,true,true,true));
    }
    function getDashboardFormFields() {
        if ($this->isAdmin()) {
            return false;
        } else if ($this->isManager()) {
            return array('project_id','name','descr_original','priority','status','estimate','requester_id','assigned_id');
        } else if ($this->isDeveloper()) {
            return array('project_id','name','descr_original','priority','status','estimate','requester_id','assigned_id');
        } else if ($this->isClient()) {
            return array('name','descr_original','priority','status','requester_id','assigned_id');
        } else if ($this->isSystem()) {
            return false;
        } else if ($this->isSales()) {
            return array('project_id','name','descr_original','priority','status','estimate','requester_id','assigned_id');
        }
        throw $this->exception('Wrong role');
    }
    function getDashboardGridFields() {
        if ($this->isAdmin()) {
            return false;
        } else if ($this->isManager()) {
            return array('project','name','priority','status','estimate','spent_time','requester','assigned','updated_dts');
        } else if ($this->isDeveloper()) {
            return array('project','name','priority','status','estimate','spent_time','requester','assigned','updated_dts');
        } else if ($this->isClient()) {
            return array('project','name','priority','status','estimate','requester','assigned','updated_dts');
        } else if ($this->isSystem()) {
            return false;
        } else if ($this->isSales()) {
            return array('project','name','priority','status','estimate','spent_time','requester','assigned','updated_dts');
        }
        throw $this->exception('Wrong role');
    }



    /* **********************************
     *
     *    FLOATING TOTAL ACCESS RULES
     *
     */
    function getFloatingTotalFields() {
        if ($this->isAdmin()) {
            return false;
        } else if ($this->canSeeFinance()) {
            return array('estimated','estimpay');
        } else if ($this->isManager()) {
            return array('estimated');
        } else if ($this->isDeveloper()) {
            return array('estimated');
        } else if ($this->isClient()) {
            return array('estimpay');
        } else if ($this->isSystem()) {
            return false;
        } else if ($this->isSales()) {
            return array('estimpay');
        }
        throw $this->exception('Wrong role');
    }






    /* **********************************
     *
     *           USER RIGHTS
     *
     */

    function canSeeRequirements() {
        return $this->checkRoleSimpleRights(array(false,true,true,true,false,true));
    }
    function canSendRequestForQuotation() {
        return $this->checkRoleSimpleRights(array(false,true,false,true,false));
    }
    function canUserMenageClients() {
        return $this->checkRoleSimpleRights(array(false,true,false,false,false));
    }



    function canSeeQuotesList() {
        return $this->checkRoleSimpleRights(array(false,true,true,true,false,true));
    }
    function canSeeUserList() {
        return $this->checkRoleSimpleRights(array(true,false,false,false,false));
    }
    function canSeeDevList() {
        return $this->checkRoleSimpleRights(array(true,false,false,false,false));
    }
    function canSeeDeleted() {
        return $this->checkRoleSimpleRights(array(false,true,false,false,false));
    }
    function canSeeReportList() {
        return $this->checkRoleSimpleRights(array(false,true,true,false,false));
    }
    function canSeeProjectList() {
        return $this->checkRoleSimpleRights(array(false,true,true,true,false));
    }
    function canSeeTaskList() {
        return $this->checkRoleSimpleRights(array(false,true,true,true,false));
    }
    function canSeeLogs() {
        return $this->checkRoleSimpleRights(array(true,false,false,false,false));
    }


    function checkRoleSimpleRights($rights) {
        if ($this->isAdmin()) {
            return $rights[0];
        } else if ($this->isManager()) {
            return $rights[1];
        } else if ($this->isDeveloper()) {
            return $rights[2];
        } else if ($this->isClient()) {
            return $rights[3];
        } else if ($this->isSystem()) {
            return $rights[4];
        } else if ($this->isSales()) {
            return $rights[5];
        } else {
            throw $this->exception('Wrong role');
        }
    }
}
