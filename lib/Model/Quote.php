<?php
class Model_Quote extends Model_Quote_Base {
    function init(){
        parent::init(); //$this->debug();
        $this->addCondition('is_deleted',false);
    }
    function in_archive(){
        $this->set('is_archived',true);
    }
    function activate(){
        $this->set('is_archived',false);
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

        if ($user->isAdmin()) {
            return false;
        } else if ($user->isManager()) {
            if ( !in_array($this['status'],$cannot_toggle_statuses) ) {
                return true;
            }
            return false;
        } else if ($user->isDeveloper()) {
            return false;
        } else if ($user->isClient()) {
            if ( !in_array($this['status'],$cannot_toggle_statuses) ) {
                return true;
            }
            return false;
        }
        throw $this->exception('Wrong role');
    }

    // ONLY manager and client have access to quote price
    function canUserSeePrice($user) {
        return $user->checkRoleSimpleRights(array(false,true,false,true,false));
    }

    function canUserSeeRequirements($user) {
    }

    function canUserSeeQuote($user) {
    }

    function canUserDeleteRequirement($user) {
        return $user->checkRoleSimpleRights(array(false,true,false,false,false));
    }

    function canUserDeleteQuote($user) {
        return $user->checkRoleSimpleRights(array(false,true,false,false,false));
    }

    function canUserAddQuote($user) {
        return $user->canSendRequestForQuotation();
    }

    // ONLY developer have access to estimate quotes with status 'estimate_needed'
    function canUserEstimateQuote($user) {
        if ($user->isAdmin()) {
            return false;
        } else if ($user->isManager()) {
            return false;
        } else if ($user->isDeveloper()) {
            if ($this['status']=='estimate_needed') {
                return true;
            }
            return false;
        } else if ($user->isClient()) {
            return false;
        }
        throw $this->exception('Wrong role');
    }

    // ONLY client and manager have access to estimate quotes with status 'estimated'
    function canUserApproveQuote($user) {
        if ( ($user->isManager()) || ($user->isClient()) ) {
            if ($this['status']=='estimated') {
                return true;
            }
        }
        return false;
    }

    function canUserEditQuote($user) {
        return $user->checkRoleSimpleRights(array(false,true,false,false,false));
    }

    function canUserRequestForEstimate($user) {
        // manager can send request for estimate if status 'quotation_requested' or 'not_estimated'
        if ($user->isAdmin()) {
            return false;
        } else if ($user->isManager()) {
            if ( $this['status']=='quotation_requested' || $this['status']=='not_estimated' ) {
                return true;
            }
            return false;
        } else if ($user->isDeveloper()) {
            return false;
        } else if ($user->isClient()) {
            return false;
        }
        throw $this->exception('Wrong role');
    }

    function canUserReadRequirements($user) {
        if ($user->isAdmin()) {
            return true;
        } else if ($user->isManager()) {
            return true;
        } else if ($user->isDeveloper()) {
            if ($this['status'] != 'quotation_requested') {
                return true;
            }
            return false;
        } else if ($user->isClient()) {
            // TODO !!!!!  ~~>  client have access to quotes of its projects ONLY!
            return true;
        }
        throw $this->exception('Wrong role');
    }

    function canUserEditRequirements($user) {
        if ($user->isAdmin()) {
            return false;
        } else if ($user->isManager()) {
            return true;
        } else if ($user->isDeveloper()) {
            if ($this['status'] == 'estimate_needed') {
                return true;
            }
            return false;
        } else if ($user->isClient()) {
            if ($this['status']=='quotation_requested' || $this['status']=='not_estimated') {
                return true;
            }
            return false;
        }
        throw $this->exception('Wrong role');
    }

    function whatRequirementFieldsUserCanEdit($user) {
        if ($user->isAdmin()) {
            return array();
        } else if ($user->isManager()) {
            return array('name','descr','estimate','file_id');
        } else if ($user->isDeveloper()) {
            return array('estimate');
        } else if ($user->isClient()) {
            return array('name','descr','file_id');
        }
        throw $this->exception('Wrong role');
    }

    function whatRequirementFieldsUserCanSee($user) {
        if ($user->isAdmin()) {
            return false;
        } else if ($user->isManager()) {
            return array('is_included','name','estimate','cost','spent_time','file','user','count_comments');
        } else if ($user->isDeveloper()) {
            return array('is_included','name','estimate','spent_time','file','user','count_comments');
        } else if ($user->isClient()) {
            return array('is_included','name','cost','file','count_comments');
        }
        throw $this->exception('Wrong role');
    }

    function whatQuoteFieldsUserCanSee($user) {
        if ($user->isAdmin()) {
            return array();
        } else if ($user->isManager()) {
            return array('project','user','name','estimated','estimpay','spent_time','rate','currency','durdead','status','warranty_end','updated_dts','expires_dts');
        } else if ($user->isDeveloper()) {
            return array('project','user','name','estimated','spent_time','durdead','status','warranty_end','updated_dts','expires_dts');
        } else if ($user->isClient()) {
            return array('project','name','estimated','estimpay','rate','currency','durdead','status','warranty_end','updated_dts','expires_dts');
        }
        throw $this->exception('Wrong role');
    }

    function whatQuoteFieldsUserCanEdit($user) {
        if ($user->isAdmin()) {
            return array();
        } else if ($user->isFinancial()) {
            return array('name','project_id','general_description','rate','currency','duration','deadline','status','warranty_end','expires_dts');
        } else if ($user->isManager()) {
            return array('name','project_id','general_description','duration','deadline','status','warranty_end','expires_dts');
        } else if ($user->isDeveloper()) {
            return array();
        } else if ($user->isClient()) {
            return array();
        }
        throw $this->exception('Wrong role');
    }

    function userAllowedActions($user,$mode) {
        if ($user->isAdmin()) {
            return array();
        } else if ($user->isManager()) {
            return array('requirements','estimation','send_to_client','approve',$mode,);
        } else if ($user->isDeveloper()) {
            return array('details','estimate',$mode,);
        } else if ($user->isClient()) {
            return array('details','edit_details','approve',);
        }
        throw $this->exception('Wrong role');
    }

    function canStatusBeChangedToEstimated($requirements=null) {
        if (!$this->canUserEstimateQuote($this->api->currentUser()))
            throw $this->exception('User with this role cannot estimate quote.','Exception_Denied');

        if (!$requirements) {
            $requirements = $this->add('Model_Requirement');
            $requirements->addCondition('quote_id',$this->id);
        }

        foreach ($requirements as $requirement){
            if($requirement['is_included'] && (($requirement['estimate']==null) || ($requirement['estimate']==0))) {
                return false;
            }
        }
        return true;
    }
    function showExpiredBox(){
        if($this->get('status')!='estimation_approved' && $this->get('status')!='finished'){
            return true;
        }
        return false;
    }
    function isExpired(){
        if($this->get('status')!='estimation_approved' && $this->get('status')!='finished'){
            if (strtotime($this->get('expires_dts'))<time()){
                return true;
            }
        }
        return false;
    }
    function userAllowedArchive($user) {
        if ($user->isAdmin()) {
            return true;
        } else if ($user->isManager()) {
            return true;
        } else if ($user->isDeveloper()) {
            return true;
        } else if ($user->isClient()) {
            return false;
        }
        throw $this->exception('Wrong role');
    }


}
