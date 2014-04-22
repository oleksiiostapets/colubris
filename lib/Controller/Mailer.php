<?php
class Controller_Mailer extends AbstractController {
    public $receivers=array();
    public $task_status='';
    function setReceivers($r){
        $this->receivers=array();
        foreach($r as $receiver){
            if(!in_array($receiver,$this->receivers)){
                if($this->isEmail($receiver)) $this->receivers[]=$receiver;
            }
        }
        return $this;
    }
    /*
     * $id - id from table user
     * $mode - users have fields that are define if mail should be send. Set TRUE if must be send obligatory
     * $exclude_myself - exclude my email from receivers
     */
    function addReceiverByUserId($id,$mode,$exclude_myself=true){
        if ($id>0){
            $u=$this->add('Model_User')->getActive();
			$u->load($id);
            // Check if the user wants to get email
            if( ($mode===true) || ($u[$mode]) ) {
                // Check if user is client and status of task not restricted to view by clients
                if((!$u['is_client']) ||
                    (($u['is_client']) &&
                        (($this->task_status!='started') && ($this->task_status!='finished'))
                    )
                ){
                    if(!in_array($u['email'],$this->receivers)){
                        if ( (!$exclude_myself) || ($u['id']!=$this->api->auth->model['id']) ){
                            if($this->isEmail($u['email'])) $this->receivers[]=$u['email'];
                        }
                    }
                }
            }
        }
        return $this;
    }
    /*
     */
    function addAllManagersReceivers($organisation_id){
        $u=$this->add('Model_User');
        $u->addCondition('organisation_id',$organisation_id);
        $u->addCondition('is_manager',true);
        foreach ($u->getRows() as $user){
            if(!in_array($user['email'],$this->receivers)){
                if($this->isEmail($user['email'])) $this->receivers[]=$user['email'];
            }
        }
        return $this;
    }
    /*
     */
    function addClientReceiver($project_id){
        $p=$this->add('Model_Project');
        $p->addCondition('id',$project_id);

        $c=$p->join('client.id','client_id','left','_c');
        $c->addField('email','email');
        foreach ($p->getRows() as $project){
            if(!in_array($project['email'],$this->receivers)){
                if($this->isEmail($project['email'])) $this->receivers[]=$project['email'];
            }
        }
        return $this;
    }
    function isEmail($email){
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return false;
        }else{
            return true;
        }
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
            if ($template=='send_quote') $this->receivers[]='a@agiletech.ie';
            $mail->send(implode(',',$this->receivers));
        }
        $this->setReceivers(array());
        $this->task_status='';
    }
}
