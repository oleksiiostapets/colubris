<?php
class Model_User_Base extends Model_BaseTable {
    public $table='user';
    function init(){
        parent::init(); //$this->debug();
        if (@$this->api->auth) $this->api->auth->addEncryptionHook($this);

        // fields
        $this->addField('email')->mandatory('required');
        $this->addField('name');
        $this->addField('password')->display(array('form'=>'password'))->mandatory('required');
        $this->addField('is_admin')->type('boolean');
        $this->addField('is_manager')->type('boolean');
        $this->addField('is_developer')->type('boolean')->caption('Is Team Member');
//        $this->addField('is_timereport')->type('boolean')->caption('Is Time Reports');
        $this->addField('hash');
        $this->addField('mail_task_changes')->type('boolean')->caption('Send when task changed');
        $this->addField('is_deleted')->type('boolean')->defaultValue('0');
        $this->addField('is_system')->defaultValue('0')->type('boolean');
        $this->addField('client_id')->refModel('Model_Client');

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
        $this->addCondition('id',$this->api->auth->get('id'));
        return $this;
    }
    function beforeInsert(&$d){
        $d['hash']=md5(uniqid());
        return parent::beforeInsert($d);
    }
    function resetPassword(){
        throw $this->exception('Function resetPassword is not implemented yet');
    }



    /* *********************************
     *
     *             GET ROLES
     *
     */
    function isAdmin() {
        return ($this['is_admin']?true:false);
    }
    function isDeveloper() {
        return ($this['is_developer']?true:false);
    }
    function isClient() {
        return ($this['is_client']?true:false);
    }
    function isManager() {
        return ($this['is_manager']?true:false);
    }
    function isSystem() {
        return ($this['is_system']?true:false);
    }




    /* **********************************
     *
     *          CURRENT USER ROLE
     *
     */
    function isCurrentUserSystem() {
        return ($this->api->getCurrentUserRole() == 'system');
    }
    function isCurrentUserAdmin() {
        return ($this->api->getCurrentUserRole() == 'admin');
    }
    function isCurrentUserManager() {
        return ($this->api->getCurrentUserRole() == 'manager');
    }
    function isCurrentUserDev() {
        return ($this->api->getCurrentUserRole() == 'developer');
    }
    function isCurrentUserClient() {
        return ($this->api->getCurrentUserRole() == 'client');
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
        if ($this->isCurrentUserAdmin()) {
            return false;
        } else if ($this->isCurrentUserManager()) {
            return array('name','descr','client_id','demo_url','prod_url','repository');
        } else if ($this->isCurrentUserDev()) {
            return array('name','descr','client_id','demo_url','prod_url');
        } else if ($this->isCurrentUserClient()) {
            return array('name','descr');
        }
        throw $this->exception('Wrong role');
    }
    function getProjectGridFields() {
        if ($this->isCurrentUserAdmin()) {
            return false;
        } else if ($this->isCurrentUserManager()) {
            return array('name','descr','client','demo_url','prod_url','repository');
        } else if ($this->isCurrentUserDev()) {
            return array('name','descr','client','demo_url','prod_url','repository');
        } else if ($this->isCurrentUserClient()) {
            return array('name','descr','client','demo_url','prod_url');
        }
        throw $this->exception('Wrong role');
    }



    /* **********************************
     *
     *      DASHBOARD ACCESS RULES
     *
     */
    function canSeeDashboard() {
        return $this->checkRoleSimpleRights(array(false,true,true,true,false));
    }
    function getDashboardFormFields() {
        if ($this->isCurrentUserAdmin()) {
            return false;
        } else if ($this->isCurrentUserManager()) {
            return array('project_id','name','descr_original','priority','status','estimate','requester_id','assigned_id');
        } else if ($this->isCurrentUserDev()) {
            return array('project_id','name','descr_original','priority','status','estimate','requester_id','assigned_id');
        } else if ($this->isCurrentUserClient()) {
            return array('name','descr_original','priority','status','requester_id','assigned_id');
        }
        throw $this->exception('Wrong role');
    }
    function getDashboardGridFields() {
        if ($this->isCurrentUserAdmin()) {
            return false;
        } else if ($this->isCurrentUserManager()) {
            return array('project','name','priority','status','estimate','spent_time','requester','assigned','updated_dts');
        } else if ($this->isCurrentUserDev()) {
            return array('project','name','priority','status','estimate','spent_time','requester','assigned','updated_dts');
        } else if ($this->isCurrentUserClient()) {
            return array('project','name','priority','status','estimate','spent_time','requester','assigned','updated_dts');
        }
        throw $this->exception('Wrong role');
    }






    /* **********************************
     *
     *           USER RIGHTS
     *
     */

    function canSendRequestForQuotation() {
        return $this->checkRoleSimpleRights(array(false,true,false,true,false));
    }
    function canUserMenageClients() {
        return $this->checkRoleSimpleRights(array(false,true,false,false,false));
    }



    function canSeeQuotesList() {
        return $this->checkRoleSimpleRights(array(false,true,true,true,false));
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
        return $this->checkRoleSimpleRights(array(false,true,true,true,false));
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
        if ($this->isCurrentUserAdmin()) {
            return $rights[0];
        } else if ($this->isCurrentUserManager()) {
            return $rights[1];
        } else if ($this->isCurrentUserDev()) {
            return $rights[2];
        } else if ($this->isCurrentUserClient()) {
            return $rights[3];
        } else if ($this->isCurrentUserSystem()) {
            return $rights[4];
        } else {
            throw $this->exception('Wrong role');
            //return false;
        }
    }
}
