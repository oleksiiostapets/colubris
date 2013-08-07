<?php
class Controller_Mailer extends AbstractController {
    function sendMail($to,$template,$options) {
    	$this->from=$this->api->getConfig('tmail/from','test@test.com');
    	
    	$mail = $this->add('TMail');
    	$mail->loadTemplate($template);
    	$mail->setTag('from',$this->from);
    	foreach($options as $val=>$key){
    		$mail->setTag($key,$val);
    	}
    	$mail->send($to);
    	 
    }
}