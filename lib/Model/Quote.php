<?php
class Model_Quote extends Model_Quote_Base {
    function init(){
        parent::init(); //$this->debug();
        $this->addCondition('is_deleted',false);
    }
    function approve() {
        $not_included_requirements = $this->add('Model_Requirement') //->debug()
                ->addCondition('quote_id',$this->id)
                ->addCondition('is_included',false)
                ->getRows();

        if (count($not_included_requirements)) {
            $this->moveRequirmsToOtherQuote(
                $not_included_requirements, $this->cloneThisQuote()
            );
        }

        $this->set('status','estimation_approved');
        $this->save();
    }
    function cloneThisQuote() {
        return $this->add('Model_Quote')
            ->set('project_id',$this['project_id'])
            ->set('user_id',$this['user_id'])
            ->set('name',$this['name'].' (not included requirements)')
            ->set('status',$this['status'])
            ->set('currency',$this['currency'])
            ->set('rate',$this['rate'])
            ->set('organisation_id',$this['organisation_id'])
            ->save()
        ;
    }
    function moveRequirmsToOtherQuote($reqs_arr,$other_quote) {
        $req = $this->add('Model_Requirement'); //->debug();
        foreach ($reqs_arr as $req_arr) {
            $req->tryLoad($req_arr['id']);
            if ($req->loaded()) {
                $req
                    ->set('quote_id',$other_quote->id)
                    ->set('is_included',true)
                    ->saveAndUnload();
            }
        }
    }
    function sendEmailToClient() {
       	if ($this['client_id']>0){
       		return $this->add('Model_Client')->load($this['client_id'])->sendQuoteEmail($this->id);
       	} else {
            throw $this->exception('The project of this quote has no client!','Exception_QuoteHasNoClient');
       	}
    }



    /* **********************************
     *
     *      QUOTE ACCESS RULES
     *
     */
    // check if this user can change 'is_included' flag of requirement
    function canUserChangeIsIncluded($user) {
        $cannot_toggle_statuses = array('estimation_approved','finished',);

        // accesses by role because user can have multiple roles
        $has_admin_access   = false;
        $has_manager_access = false;
        $has_dev_access     = false;
        $has_client_access  = false;

        // admin cannot toggle requirements
        if ($user['is_admin']) $has_admin_access = false;

        // manager can toggle requirements if status is not 'estimation_approved' or 'finished'
        if ( $user['is_manager'] )
        if ( !in_array($this['status'],$cannot_toggle_statuses) ) {
            $has_manager_access = true;
        }

        // dev cannot toggle requirements
        if ($user['is_developer']) $has_dev_access = false;

        // client can toggle requirements if status is not 'estimation_approved' or 'finished'
        if ( $user['is_client'] )
        if ( !in_array($this['status'],$cannot_toggle_statuses) ) {
            $has_client_access = true;
        }

        return ($has_admin_access || $has_manager_access || $has_dev_access || $has_client_access);
    }

    // ONLY manager and client have access to quote price
    function canUserSeePrice($user) {
        return ($user['is_manager'] || $user['is_client']);
    }

    function canUserSeeRequirements($user) {
    }

    function canUserSeeQuote($user) {
    }

    function canUserDeleteRequirement($user) {
        return $user['is_manager'];
    }

    function canUserDeleteQuote($user) {
        return $user['is_manager'];
    }

    function canUserAddQuote($user) {
        return $user->canSendRequestForQuotation();
    }

    // ONLY developer have access to estimate quotes with status 'estimate_needed'
    function canUserEstimateQuote($user) {
        if ($user['is_developer'] && $this['status']=='estimate_needed') {
            return true;
        }
        return false;
    }

    function canUserEditQuote($user) {

        // accesses by role because user can have multiple roles
        $has_admin_access   = false;
        $has_manager_access = false;
        $has_dev_access     = false;
        $has_client_access  = false;

        // admin cannot
        if ($user['is_admin']) $has_admin_access = false;

        // manager can
        if ($user['is_manager']) $has_manager_access = true;

        // dev cannot
        if ($user['is_developer']) $has_dev_access = false;

        // client cannot
        if ($user['is_client']) $has_client_access = false;

        return ($has_admin_access || $has_manager_access || $has_dev_access || $has_client_access);
    }

    function canUserRequestForEstimate($user) {
        // manager can send request for estimate if status 'quotation_requested' or 'not_estimated'
        if ($user['is_manager'])
        if ( $this['status']=='quotation_requested' || $this['status']=='not_estimated' ) {
            return true;
        }
        return false;
    }

    function canUserReadRequirements($user) {

        // accesses by role because user can have multiple roles
        $has_admin_access   = false;
        $has_manager_access = false;
        $has_dev_access     = false;
        $has_client_access  = false;

        // admin don't have access to quote
        if ($user['is_admin']) $has_admin_access = true;

        // manager have access to all quotes
        if ($user['is_manager']) $has_manager_access = true;

        // dev have no access to projects
        if ($user['is_developer'])
        if ($this['status'] != 'quotation_requested') {
            $has_dev_access = true;
        }

        // TODO !!!!!  ~~>  client have access to quotes of its projects ONLY!
        if ($user['is_client']) {
            $has_client_access = true;
        }

        return ($has_admin_access || $has_manager_access || $has_dev_access || $has_client_access);
    }

    function canUserEditRequirements($user) {

        // accesses by role because user can have multiple roles
        $has_admin_access   = false;
        $has_manager_access = false;
        $has_dev_access     = false;
        $has_client_access  = false;

        // admin don't have access to quote
        if ($user['is_admin']) $has_admin_access = false;

        // manager have access to all quotes
        if ($user['is_manager']) $has_manager_access = true;

        // dev have no access to projects
        if ($user['is_developer'])
        if ($this['status'] == 'estimate_needed') {
            $has_dev_access = true;
        }

        // client have access to quotes with statuses 'quotation_requested' and 'not_estimated'
        if ($user['is_client'] && ($this['status']=='quotation_requested' || $this['status']=='not_estimated' )) {
            $has_client_access = true;
        }

        return ($has_admin_access || $has_manager_access || $has_dev_access || $has_client_access);
    }

    function whatRequirementFieldsUserCanEdit($user) {

        $fields_total = array();

        // accesses by role because user can have multiple roles
        $admin_fields   = array();
        $manager_fields = array('name','descr','estimate','file_id');
        $dev_fields     = array('estimate');
        $client_fields  = array('name','descr','file_id');

        // admin don't have access to quote
        if ($user['is_admin']) $fields_total = array_merge($fields_total, $admin_fields);

        // manager have access to all quotes
        if ($user['is_manager']) $fields_total = array_merge($fields_total, $manager_fields);

        // dev have no access to projects
        if ($user['is_developer']) $fields_total = array_merge($fields_total, $dev_fields);

        // client have access to quotes with statuses 'quotation_requested' and 'not_estimated'
        if ($user['is_client'] ) $fields_total = array_merge($fields_total, $client_fields);

        return array_unique($fields_total);
    }

    function whatQuoteFieldsUserCanSee($user) {

        $fields_total = array();

        // accesses by role because user can have multiple roles
        $admin_fields   = array();
        $manager_fields = array('project','user','name','estimated','estimpay','spent_time','rate','currency','durdead','status');
        $dev_fields     = array('project','user','name','estimated','spent_time','durdead','status');
        $client_fields  = array('project','user','name','estimated','estimpay','spent_time','rate','currency','durdead','status');

        // admin don't have access to quote
        if ($user['is_admin']) $fields_total = array_merge($fields_total, $admin_fields);

        // manager have access to all quotes
        if ($user['is_manager']) $fields_total = array_merge($fields_total, $manager_fields);

        // dev have no access to projects
        if ($user['is_developer']) $fields_total = array_merge($fields_total, $dev_fields);

        // client have access to quotes with statuses 'quotation_requested' and 'not_estimated'
        if ($user['is_client'] ) $fields_total = array_merge($fields_total, $client_fields);

        return array_unique($fields_total);
    }

    function whatQuoteFieldsUserCanEdit($user) {

        $fields_total = array();

        // accesses by role because user can have multiple roles
        $admin_fields   = array();
        $manager_fields = array('name','project_id','general','rate','currency','duration','deadline','status');
        $dev_fields     = array();
        $client_fields  = array();

        // admin don't have access to quote
        if ($user['is_admin']) $fields_total = array_merge($fields_total, $admin_fields);

        // manager have access to all quotes
        if ($user['is_manager']) $fields_total = array_merge($fields_total, $manager_fields);

        // dev have no access to projects
        if ($user['is_developer']) $fields_total = array_merge($fields_total, $dev_fields);

        // client have access to quotes with statuses 'quotation_requested' and 'not_estimated'
        if ($user['is_client'] ) $fields_total = array_merge($fields_total, $client_fields);

        return array_unique($fields_total);
    }

    function userAllowedActions($user) {

        $actions_total = array();

        // accesses by role because user can have multiple roles
        $admin_actions   = array();
        $manager_actions = array('requirements','estimation','send_to_client','approve',);
        $dev_actions     = array('details','estimate',);
        $client_actions  = array('details','edit_details','approve',);

        // admin don't have access to quote
        if ($user['is_admin']) $actions_total = array_merge($actions_total, $admin_actions);

        // manager have access to all quotes
        if ($user['is_manager']) $actions_total = array_merge($actions_total, $manager_actions);

        // dev have no access to projects
        if ($user['is_developer']) $actions_total = array_merge($actions_total, $dev_actions);

        // client have access to quotes with statuses 'quotation_requested' and 'not_estimated'
        if ($user['is_client'] ) $actions_total = array_merge($actions_total, $client_actions);

        return array_unique($actions_total);
    }


}
