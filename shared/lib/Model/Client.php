<?php
class Model_Client extends Model_BaseTable {
	public $table='client';

	function init(){
		parent::init();

        //fields
		$this->newField('name');

		$this->addField('email');
		$this->addField('phone');
        $this->addField('is_archive')->type('boolean');
        $this->addField('avatar_id');

        $this->addField('is_deleted')->type('boolean')->defaultValue('0');
//        $this->addField('deleted_id')->refModel('Model_User');
		$this->hasOne('User','deleted_id');

//        $this->addField('organisation_id')->refModel('Model_Organisation');
		$this->hasOne('Organisation','organisation_id');

        // expressions
        $this->addExpression('avatar')->set(function($m,$q){
            return $q->dsql()
                ->table('filestore_file')
                ->field('filename')
                ->where('filestore_file.id',$q->getField('avatar_id'))
                ;
        });
        $this->addExpression('avatar_thumb')->set(function($m,$q){
            return $q->dsql()
                ->table('filestore_file')
                ->table('filestore_image')
                ->field('filename')
                ->where('filestore_image.original_file_id',$q->getField('avatar_id'))
                ->where('filestore_image.thumb_file_id=filestore_file.id')
                ;
        });


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
		//$this->addCondition('organisation_id',$this->app->currentUser()->get('organisation_id'));
		$this->addCondition('is_deleted',true);
		return $this;
	}
	function notDeleted() {
		$this->addCondition('is_deleted',false);
		return $this;
	}
	function getThisOrganisation() {
		//$this->addCondition('organisation_id',$this->app->currentUser()->get('organisation_id'));
		return $this;
	}

    function sendQuoteEmail($quote_id) {
        if ($this['email']!=''){
            $quote=$this->add('Model_Quote')->notDeleted()->getThisOrganisation()->load($quote_id);
            $this->api->mailer->addClientReceiver($quote->get('project_id'));
            //$this->api->mailer->setReceivers(array($this['name'].' <'.$this['email'].'>'));//'"'.$this['name'].' <'.$this['email'].'>"'));
            $this->api->mailer->sendMail('send_quote',array(
                'link'=>$this->api->siteURL().$this->api->url('quotes/'.$quote_id),
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
