<?
class Model_Client extends Model_Client_Base {
    function init(){
        parent::init();
        $this->addCondition('is_deleted',false);
    }
    function sendQuoteEmail($quote_id) {
        if ($this['email']!=''){
            $this->api->mailer->setReceivers(array($this['email']));
            $this->api->mailer->sendMail('send_quote',array(
               'link'=>$this->api->siteURL().$this->api->url('client/quotes/rfq/estimated',array('quote_id'=>$quote_id))
            ),true);
        } else {
            throw $this->exception('The project of this quote has no client!','Exception_ClientHasNoEmail')
                    ->addMoreInfo('name',$this['name'])
            ;
        }
        return $this;
    }
}
