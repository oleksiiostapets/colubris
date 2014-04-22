<?php
class Model_Client extends Model_BaseTable {
	public $table='client';

	function init(){
		parent::init();

		$this->newField('name');

		$this->addField('email');
		$this->addField('phone');
		$this->addField('is_archive')->type('boolean');

		$this->addField('is_deleted')->type('boolean')->defaultValue('0');
//        $this->addField('deleted_id')->refModel('Model_User');
		$this->hasOne('User','deleted_id');

//        $this->addField('organisation_id')->refModel('Model_Organisation');
		$this->hasOne('Organisation','organisation_id');

		$this->addHooks();
	}

	// ------------------------------------------------------------------------------
	//
	//            HOOKS :: BEGIN
	//
	// ------------------------------------------------------------------------------

	function addHooks() {
		$this->addHook('beforeDelete', function($m){
			$m['deleted_id']=$m->api->currentUser()->get('id');
		});
	}

	function deleted() {
		$this->addCondition('organisation_id',$this->api->auth->model['organisation_id']);
		$this->addCondition('is_deleted',true);
		return $this;
	}
	function notDeleted() {
		$this->addCondition('is_deleted',false);
		return $this;
	}
	function getThisOrganisation() {
		$this->addCondition('organisation_id',$this->api->auth->model['organisation_id']);
		return $this;
	}

    function sendQuoteEmail($quote_id) {
        if ($this['email']!=''){
            $quote=$this->add('Model_Quote')->notDeleted()->getThisOrganisation()->load($quote_id);
            $this->api->mailer->addClientReceiver($quote->get('project_id'));
            //$this->api->mailer->setReceivers(array($this['name'].' <'.$this['email'].'>'));//'"'.$this['name'].' <'.$this['email'].'>"'));
            $this->api->mailer->sendMail('send_quote',array(
                'link'=>$this->api->siteURL().$this->api->url('quotes/rfq/requirements',array('quote_id'=>$quote_id)),
                'username'=>$this['name'],
                'quotename'=>$quote['name'],
//               'link'=>$this->api->siteURL().$this->api->url('client/quotes/rfq/estimated',array('quote_id'=>$quote_id))
            ),true);
        } else {
            throw $this->exception('The project of this quote has no client!','Exception_ClientHasNoEmail')
                    ->addMoreInfo('name',$this['name'])
            ;
        }
        return $this;
    }
    function forDeveloper() {
        $mp=$this->add('Model_Project')->notDeleted();
        $mp=$mp->forDeveloper();
        $ids="";
        foreach ($mp as $p){
            if($ids=="") $ids=$p['client_id'];
            else $ids=$ids.','.$p['client_id'];
        }
        $this->addCondition('id','in',$ids);
        return $this;
    }
}
