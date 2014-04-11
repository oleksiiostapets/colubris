<?php
/**
 * Created by Vadym Radvansky
 * Date: 4/1/14 4:45 PM
 */
class Controller_UserAccess extends AbstractController {
    public $auto_track_element = true;
    protected $user = false;
    function init() {
        parent::init();
    }
    function setUser(Model $m) {
        $this->user = $m;
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
        if ($this->user->isAdmin()) {
            return false;
        } else if ($this->user->isManager()) {
            return array('name','descr','client_id','demo_url','prod_url','repository');
        } else if ($this->user->isDeveloper()) {
            return array('name','descr','client_id','demo_url','prod_url');
        } else if ($this->user->isClient()) {
            return array('name','descr');
        } else if ($this->user->isSystem()) {
            return false;
        }
        throw $this->exception('Wrong role');
    }
    function getProjectGridFields() {
        if ($this->user->isAdmin()) {
            return false;
        } else if ($this->user->isManager()) {
            return array('name','descr','client','demo_url','prod_url','repository');
        } else if ($this->user->isDeveloper()) {
            return array('name','descr','client','demo_url','prod_url','repository');
        } else if ($this->user->isClient()) {
            return array('name','descr','client','demo_url','prod_url');
        } else if ($this->user->isSystem()) {
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
        if ($this->user->isAdmin()) {
            return false;
        } else if ($this->user->isManager()) {
            return array('project_id','name','descr_original','priority','status','estimate','requester_id','assigned_id');
        } else if ($this->user->isDeveloper()) {
            return array('project_id','name','descr_original','priority','status','estimate','requester_id','assigned_id');
        } else if ($this->user->isClient()) {
            return array('name','descr_original','priority','status','requester_id','assigned_id');
        } else if ($this->user->isSystem()) {
            return false;
        } else if ($this->user->isSales()) {
            return array('project_id','name','descr_original','priority','status','estimate','requester_id','assigned_id');
        }
        throw $this->exception('Wrong role');
    }
    function getDashboardGridFields() {
        if ($this->user->isAdmin()) {
            return false;
        } else if ($this->user->isManager()) {
            return array('project','name','priority','status','estimate','spent_time','requester','assigned','updated_dts');
        } else if ($this->user->isDeveloper()) {
            return array('project','name','priority','status','estimate','spent_time','requester','assigned','updated_dts');
        } else if ($this->user->isClient()) {
            return array('project','name','priority','status','estimate','requester','assigned','updated_dts');
        } else if ($this->user->isSystem()) {
            return false;
        } else if ($this->user->isSales()) {
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
        if ($this->user->isAdmin()) {
            return false;
        } else if ($this->user->canSeeFinance()) {
            return array('estimated','estimpay');
        } else if ($this->user->isManager()) {
            return array('estimated');
        } else if ($this->user->isDeveloper()) {
            return array('estimated');
        } else if ($this->user->isClient()) {
            return array('estimpay');
        } else if ($this->user->isSystem()) {
            return false;
        } else if ($this->user->isSales()) {
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

    // tasks
    function whatTaskFieldsUserCanEdit() {
        if ($this->user->isAdmin()) {
            return array();
        } else if ($this->user->isManager()) {
            return array('project_id','quote_id','name','descr_original','priority','type','status','estimate','requester_id','assigned_id');
        } else if ($this->user->isDeveloper()) {
            return array('name','descr_original','priority','type','status','estimate','requester_id','assigned_id');
        } else if ($this->user->isClient()) {
            return array('name','descr_original','priority','type','status');
        } else if ($this->user->isSales()) {
            return array('name','descr_original','priority','type','status','estimate');
        }
        throw $this->exception('Wrong role');
    }
    function whatTaskFieldsUserCanSee($quote=null) {
        if ($this->user->isAdmin()) {
            return array();
        } else if ($this->user->isManager()) {
            return array('project','quote','name','priority','type','status','estimate','spent_time','requester','assigned');
        } else if ($this->user->isDeveloper()) {
            return array('project','quote','name','priority','type','status','estimate','spent_time','requester','assigned');
        } else if ($this->user->isClient()) {
            if (is_object($quote) && $quote['show_time_to_client']) {
                return array('project','quote','name','priority','type','status','estimate','spent_time');
            } else {
                return array('project','quote','name','priority','type','status','estimate');
            }
        } else if ($this->user->isSales()) {
            return array('project','quote','name','priority','type','status','estimate','spent_time','requester','assigned');
        }
        throw $this->exception('Wrong role');
    }


    function checkRoleSimpleRights($rights) {
        if ($this->user->isAdmin()) {
            return $rights[0];
        } else if ($this->user->isManager()) {
            return $rights[1];
        } else if ($this->user->isDeveloper()) {
            return $rights[2];
        } else if ($this->user->isClient()) {
            return $rights[3];
        } else if ($this->user->isSystem()) {
            return $rights[4];
        } else if ($this->user->isSales()) {
            return $rights[5];
        } else {
            throw $this->exception('Wrong role');
        }
    }
}