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

    // check if this user can change 'is_included' flag of requirement
    private $cannot_toggle_statuses = array('estimation_approved','finished',);
    function canToggle($user) {
        if ($user['is_developer']) {
            return false;
        }
        return (in_array($this['status'],$this->cannot_toggle_statuses)?false:true);

    }
}
