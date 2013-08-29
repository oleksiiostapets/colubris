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
    function hasUserIsIncludedAccess($user) {
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
            $has_manager_access = true;
        }

        return ($has_admin_access || $has_manager_access || $has_dev_access || $has_client_access);
    }

    // ONLY manager and client have access to quote price
    function hasUserSeePriceAccess($user) {
        return ($user['is_manager'] || $user['is_client']);
    }

    function hasUserReadAccess($user) {
    }

    // ONLY developer have access to estimate quote
    function hasUserEstimateAccess($user) {
        if ($user['is_developer']) return true;
        return false;
    }

    function hasUserRequestForEstimateAccess($user) {

        // accesses by role because user can have multiple roles
        $has_admin_access   = false;
        $has_manager_access = false;
        $has_dev_access     = false;
        $has_client_access  = false;

        // admin cannot send request for estimate
        if ($user['is_admin']) $has_admin_access = false;

        // manager can send request for estimate if status 'quotation_requested' or 'not_estimated'
        if ($user['is_manager'])
        if ( $this['status']=='quotation_requested' || $this['status']=='not_estimated' ) {
            $has_manager_access = true;
        }

        // dev cannot send request for estimate
        if ($user['is_developer']) $has_dev_access = false;

        // client cannot send request for estimate
        if ($user['is_client']) $has_dev_access = false;

        return ($has_admin_access || $has_manager_access || $has_dev_access || $has_client_access);
    }

    function hasUserDeleteAccess($user) {
    }

    function hasUserEditRequirementsAccess($user) {

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
        if ($user['is_developer']) $has_dev_access = false;

        // client have access to quotes with statuses 'quotation_requested' and 'not_estimated'
        if ($user['is_client'] && ($this['status']=='quotation_requested' || $this['status']=='not_estimated' )) {
            $has_dev_access = true;
        }

        return ($has_admin_access || $has_manager_access || $has_dev_access || $has_client_access);
    }
}
