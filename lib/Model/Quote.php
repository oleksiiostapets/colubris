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

        if ($user->isCurrentUserAdmin()) {
            return false;
        } else if ($user->isCurrentUserManager()) {
            if ( !in_array($this['status'],$cannot_toggle_statuses) ) {
                return true;
            }
            return false;
        } else if ($user->isCurrentUserDev()) {
            return false;
        } else if ($user->isCurrentUserClient()) {
            if ( !in_array($this['status'],$cannot_toggle_statuses) ) {
                return true;
            }
            return false;
        }
        throw $this->exception('Wrong role');
    }

    // ONLY manager and client have access to quote price
    function canUserSeePrice($user) {
        return $user->checkRoleSimpleRights(array(false,true,false,true));
    }

    function canUserSeeRequirements($user) {
    }

    function canUserSeeQuote($user) {
    }

    function canUserDeleteRequirement($user) {
        return $user->checkRoleSimpleRights(array(false,true,false,false));
    }

    function canUserDeleteQuote($user) {
        return $user->checkRoleSimpleRights(array(false,true,false,false));
    }

    function canUserAddQuote($user) {
        return $user->canSendRequestForQuotation();
    }

    // ONLY developer have access to estimate quotes with status 'estimate_needed'
    function canUserEstimateQuote($user) {
        if ($user->isCurrentUserAdmin()) {
            return false;
        } else if ($user->isCurrentUserManager()) {
            return false;
        } else if ($user->isCurrentUserDev()) {
            if ($this['status']=='estimate_needed') {
                return true;
            }
            return false;
        } else if ($user->isCurrentUserClient()) {
            return false;
        }
        throw $this->exception('Wrong role');
    }

    function canUserEditQuote($user) {
        return $user->checkRoleSimpleRights(array(false,true,false,false));
    }

    function canUserRequestForEstimate($user) {
        // manager can send request for estimate if status 'quotation_requested' or 'not_estimated'
        if ($user->isCurrentUserAdmin()) {
            return false;
        } else if ($user->isCurrentUserManager()) {
            if ( $this['status']=='quotation_requested' || $this['status']=='not_estimated' ) {
                return true;
            }
            return false;
        } else if ($user->isCurrentUserDev()) {
            return false;
        } else if ($user->isCurrentUserClient()) {
            return false;
        }
        throw $this->exception('Wrong role');
    }

    function canUserReadRequirements($user) {
        if ($user->isCurrentUserAdmin()) {
            return true;
        } else if ($user->isCurrentUserManager()) {
            return true;
        } else if ($user->isCurrentUserDev()) {
            if ($this['status'] != 'quotation_requested') {
                return true;
            }
            return false;
        } else if ($user->isCurrentUserClient()) {
            // TODO !!!!!  ~~>  client have access to quotes of its projects ONLY!
            return true;
        }
        throw $this->exception('Wrong role');
    }

    function canUserEditRequirements($user) {
        if ($user->isCurrentUserAdmin()) {
            return false;
        } else if ($user->isCurrentUserManager()) {
            return true;
        } else if ($user->isCurrentUserDev()) {
            if ($this['status'] != 'estimate_needed') {
                return true;
            }
            return false;
        } else if ($user->isCurrentUserClient()) {
            if ($this['status']=='quotation_requested' || $this['status']=='not_estimated') {
                return true;
            }
            return false;
        }
        throw $this->exception('Wrong role');
    }

    function whatRequirementFieldsUserCanEdit($user) {
        if ($user->isCurrentUserAdmin()) {
            return array();
        } else if ($user->isCurrentUserManager()) {
            return array('name','descr','estimate','file_id');
        } else if ($user->isCurrentUserDev()) {
            return array('estimate');
        } else if ($user->isCurrentUserClient()) {
            return array('name','descr','file_id');
        }
        throw $this->exception('Wrong role');
    }

    function whatQuoteFieldsUserCanSee($user) {
        if ($user->isCurrentUserAdmin()) {
            return array();
        } else if ($user->isCurrentUserManager()) {
            return array('project','user','name','estimated','estimpay','spent_time','rate','currency','durdead','status');
        } else if ($user->isCurrentUserDev()) {
            return array('project','user','name','estimated','spent_time','durdead','status');
        } else if ($user->isCurrentUserClient()) {
            return array('project','user','name','estimated','estimpay','spent_time','rate','currency','durdead','status');
        }
        throw $this->exception('Wrong role');
    }

    function whatQuoteFieldsUserCanEdit($user) {
        if ($user->isCurrentUserAdmin()) {
            return array();
        } else if ($user->isCurrentUserManager()) {
            return array('name','project_id','general','rate','currency','duration','deadline','status');
        } else if ($user->isCurrentUserDev()) {
            return array();
        } else if ($user->isCurrentUserClient()) {
            return array();
        }
        throw $this->exception('Wrong role');
    }

    function userAllowedActions($user) {
        if ($user->isCurrentUserAdmin()) {
            return array();
        } else if ($user->isCurrentUserManager()) {
            return array('requirements','estimation','send_to_client','approve',);
        } else if ($user->isCurrentUserDev()) {
            return array('details','estimate',);
        } else if ($user->isCurrentUserClient()) {
            return array('details','edit_details','approve',);
        }
        throw $this->exception('Wrong role');
    }


}
