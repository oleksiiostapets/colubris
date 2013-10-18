<?php
class View_SwitcherEditTask extends View {
    function init(){
        parent::init();

        $v=$this->add('View')->setClass('left');

        $f=$v->add('Form');
        $f->addClass('horizontal');
        // Project
        $mp=$this->add('Model_Project');
        if($this->api->currentUser()->isDeveloper()){
            $mp=$mp->forDeveloper();
        }elseif($this->api->currentUser()->isClient()){
            $mp=$mp->forClient();
        }
        $projects=$mp->getRows();
        if($_GET['edit_project_id']){
            $this->task->set('project_id',$_GET['edit_project_id']);
            $this->task->set('requirement_id',null);
            $this->task->save();
        }
        $p_arr=array();
        foreach ($projects as $p){
        	$p_arr[$p['id']]=$p['name'];
        }
        $fp=$f->addField('dropdown','project');
        $fp->setValueList($p_arr);
        $fp->set($this->task->get('project_id'));

        // Quote
		$mq=$this->add('Model_Quote');
		$mq->addCondition('status','IN',array('estimation_approved','estimated'));
		$mq->addCondition('project_id',$this->task->get('project_id'));
		if($_GET['edit_quote_id']!==null){
			$check=$mq->tryLoad($_GET['edit_quote_id']);
			if(!$check->loaded()){
				$_GET['edit_quote_id']=0;
			}
		}
		$q_arr=$mq->getRows();
		$qn_arr['0']='all';
		foreach($q_arr as $q){
			$qn_arr[$q['id']]=$q['name'];
		}
		if($_GET['edit_quote_id']!==null){
			$this->api->memorize('edit_quote_id',$_GET['edit_quote_id']);
            $this->task->set('requirement_id',0);
            $this->task->save();
		}else{
            if($this->task->get('requirement_id')>0){
                $cmr=$this->add('Model_Requirement')->tryLoad($this->task->get('requirement_id'));
                if($cmr->loaded()){
                    $this->api->memorize('edit_edit_id',$cmr->get('quote_id'));
                }
            }
        }
		$fq=$f->addField('dropdown','quote');
		$fq->setValueList($qn_arr);
		$fq->set($this->api->recall('edit_quote_id'));
		
		// Requirement
		$mr=$this->add('Model_Requirement');
		$mr->addCondition('quote_id',$this->api->recall('edit_quote_id'));
    	if($_GET['edit_requirement_id']!==null){
            $this->task->set('requirement_id',$_GET['edit_requirement_id']);
            $this->task->save();
		}
		$r_arr=$mr->getRows();
		$rn_arr['0']='all';
		foreach($r_arr as $r){
			$rn_arr[$r['id']]=$r['name'];
		}
		$fr=$f->addField('dropdown','requirement');
		$fr->setValueList($rn_arr);
		$fr->set($this->task->get('requirement_id'));
		
		$fp->js('change')->univ()->location(array(
            $this->api->url(),
            'edit_project_id'=>$fp->js()->val(),
        ));
		$fq->js('change')->univ()->location(array(
            $this->api->url(),
            'edit_quote_id'=>$fq->js()->val(),
        ));
		$fr->js('change')->univ()->location(array(
            $this->api->url(),
            'edit_requirement_id'=>$fr->js()->val(),
        ));
		
        $v=$this->add('View')->setClass('clear');
    }
}
