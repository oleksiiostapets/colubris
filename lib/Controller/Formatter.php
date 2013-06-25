<?php
class Controller_Formatter extends AbstractController {
    function formatDeadline($deadline,$state){
        $datetime2 = new DateTime($deadline);
        $datetime1 = new DateTime();
        $interval = $datetime1->diff($datetime2)->format('%R%a');
        if($interval<0){
            $interval=((int)(-$interval)).' days ago';
            if((int)$state<15){
                $interval='<font color="red">'.$interval.'</font>';
            };
        }elseif($interval>0){
            $interval=((int)$interval).' days ago';
        }else{
            $interval=null;
        }
        return $interval;
    }
    function formatPriority($priority){
        $c=null;
        if((int)$priority < 2)$c='gray';
        if((int)$priority == 3)$c='green';
        if((int)$priority > 3)$c='red';
        if($c)$priority='<font color="'.$c.'">'.$priority.'</font>';
        return $priority;
    }
}
