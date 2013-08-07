<?php
class Controller_Mailer extends AbstractController {
    function sendMail($to,$template,$options) {
    	$from=$this->api->getConfig('tmail/from','test@test.com');
    	
    	$mail = $this->add('TMail');
    	$mail->loadTemplate($template);
    	$mail->setTag('from',$from);
    	foreach($options as $val=>$key){
    		$mail->setTag($val,$key);
    	}
    	$mail->send($to);
    	 
    }
}