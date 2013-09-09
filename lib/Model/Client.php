<?
class Model_Client extends Model_Client_Base {
    function init(){
        parent::init();
        $this->addCondition('is_deleted',false);
    }
    function sendQuoteEmail($quote_id) {
        if ($this['email']!=''){
            $quote=$this->add('Model_Quote')->load($quote_id);
            $this->api->mailer->setReceivers(array($this['name'].' <'.$this['email'].'>'));//'"'.$this['name'].' <'.$this['email'].'>"'));
            $this->api->mailer->sendMail('send_quote',array(
                'link'=>$this->api->siteURL().$this->api->url('quotes'),
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
}
