<?php
class Controller_Mailer extends AbstractController {
    private $receivers=array();
    public $task_status='';
    function setReceivers($r){
        $this->receivers=array();
        foreach($r as $receiver){
            if(!in_array($receiver,$this->receivers)){
                $this->receivers[]=$receiver;
            }
        }
        return $this;
    }
    /*
     * $id - id from table user
     * $mode - users have fields that are define if mail should be send. Set TRUE if must be send obligatory
     */
    function addReceiverByUserId($id,$mode){
        if ($id>0){
            $u=$this->add('Model_User_Notdeleted')->load($id);
            // Check if the user whants to get email
            if( ($mode===true) || ($u[$mode]) ) {
                // Check if user is client and status of task not restricted to view by clients
                if((!$u['is_client']) ||
                    (($u['is_client']) &&
                        (($this->task_status!='started') && ($this->task_status!='finished'))
                    )
                ){
                    if(!in_array($u['email'],$this->receivers)){
                        $this->receivers[]=$u['email'];
                    }
                }
            }
        }
        return $this;
    }
    function sendMail($template,$options) {
        if(count($this->receivers)>0){
            $from=$this->api->getConfig('tmail/from','test@test.com');

            $mail = $this->add('TMail');
            $mail->loadTemplate($template);
            $mail->setTag('from',$from);
            foreach($options as $val=>$key){
                $mail->setTag($val,$key);
            }
            $mail->send(implode(',',$this->receivers));
        }
        $this->setReceivers(array());
        $this->task_status='';
    }
}