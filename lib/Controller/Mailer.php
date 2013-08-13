<?php
class Controller_Mailer extends AbstractController {
    function sendMail($to,$template,$options,$mode) {
        if ($this->needSendMail($mode)){
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
    function needSendMail($mode){
        if($mode===true) {
            return true;
        } else {
            $u=$this->add('Model_User')->load($this->api->auth->model['id']);
            return $u[$mode];
        }
    }
}