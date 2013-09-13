<?php
class View_Switcher extends View {
    function init(){
        parent::init();

        $this->api->stickyGet('project_id');
        $this->api->stickyGet('quote_id');
        $this->api->stickyGet('requirement_id');
        $this->api->stickyGet('status');
        $this->api->stickyGet('assigned_id');

        isset($this->class)?$class=$this->class:$class='right';
        $v=$this->add('View')->setClass($class);
        
        $f=$v->add('Form');
        $f->addClass('horizontal');
        // Project
        $mp=$this->add('Model_Project');
        if($this->api->currentUser()->isCurrentUserDev()){
            $mp=$mp->forDeveloper();
        }elseif($this->api->currentUser()->isCurrentUserClient()){
            $mp=$mp->forClient();
        }
        $projects=$mp->getRows();
        if($_GET['project_id']){
        	$this->api->memorize('project_id',$_GET['project_id']);
        	$this->api->memorize('quote_id',0);
        	$this->api->memorize('requirement_id',0);
        }elseif(!$this->api->recall('project_id')){
        	if(count($projects)>0){
        		$this->api->memorize('project_id',$projects[0]['id']);
        	}
        }else{
            $check=$this->add('Model_Project_Participant')->tryLoad($this->api->recall('project_id'));
            if (!$check->loaded()){
                if(count($projects)>0){
                    $this->api->memorize('project_id',$projects[0]['id']);
                }else{
                    $this->api->forget('project_id');
                }
            }
        }
        $p_arr=array();
        foreach ($projects as $p){
        	$p_arr[$p['id']]=$p['name'];
        }
        $fp=$f->addField('dropdown','project');
        $fp->setValueList($p_arr);
        $fp->set($this->api->recall('project_id'));

        // Quote
		$mq=$this->add('Model_Quote');
		$mq->addCondition('status','estimation_approved');
		$mq->addCondition('project_id',$this->api->recall('project_id'));
		if($_GET['quote_id']!==null){
			$check=$mq->tryLoad($_GET['quote_id']);
			if(!$check->loaded()){
				$_GET['quote_id']=0;
			}
		}
		$q_arr=$mq->getRows();
		$qn_arr['0']='all';
		foreach($q_arr as $q){
			$qn_arr[$q['id']]=$q['name'];
		}
		if($_GET['quote_id']!==null){
			$this->api->memorize('quote_id',$_GET['quote_id']);
        	$this->api->memorize('requirement_id',0);
		}
		$fq=$f->addField('dropdown','quote');
		$fq->setValueList($qn_arr);
		$fq->set($this->api->recall('quote_id'));
		
		// Requirement
		$mr=$this->add('Model_Requirement');
		$mr->addCondition('quote_id',$this->api->recall('quote_id'));
    	if($_GET['requirement_id']!==null){
			$check=$mr->tryLoad($_GET['requirement_id']);
			if(!$check->loaded()){
				$_GET['requirement_id']=0;
			}
		}
		$r_arr=$mr->getRows();
		$rn_arr['0']='all';
		foreach($r_arr as $r){
			$rn_arr[$r['id']]=$r['name'];
		}
		if($_GET['requirement_id']!==null){
			$this->api->memorize('requirement_id',$_GET['requirement_id']);
		}
		$fr=$f->addField('dropdown','requirement');
		$fr->setValueList($rn_arr);
		$fr->set($this->api->recall('requirement_id'));
		
		if (strpos($this->api->page,'new')===false){
			// Status
			$s_arr=array_merge(array('all'=>'all'),$this->api->task_statuses);
			if($_GET['status']!==null){
				$this->api->memorize('status',$_GET['status']);
			}else{
                if (is_null($this->api->recall('status'))) {
                    $this->api->memorize('status','all');
                }
			}
			$fs=$f->addField('dropdown','status');
			$fs->setValueList($s_arr);
			$fs->set($this->api->recall('status'));
			
			// Assigned_to
			$ma=$this->add('Model_User_Organisation')->setOrder('name');
			$a_arr=$ma->getRows();
			$u_arr['0']='all';
			foreach($a_arr as $a){
				$u_arr[$a['id']]=$a['name'];
			}
			if($_GET['assigned_id']!==null){
				$this->api->memorize('assigned_id',$_GET['assigned_id']);
			}
			$fa=$f->addField('dropdown','assigned_id','Assigned');
			$fa->setValueList($u_arr);
			$fa->set($this->api->recall('assigned_id'));
			
			$js_arr=array(
					$this->api->url(),
					'project_id'=>$fp->js()->val(),
					'quote_id'=>$fq->js()->val(),
					'requirement_id'=>$fr->js()->val(),
					'status'=>$fs->js()->val(),
					'assigned_id'=>$fa->js()->val(),
			);
			
			$fs->js('change')->univ()->location($js_arr);
			$fa->js('change')->univ()->location($js_arr);
		}else{
			$js_arr=array(
					$this->api->url(),
					'project_id'=>$fp->js()->val(),
					'quote_id'=>$fq->js()->val(),
					'requirement_id'=>$fr->js()->val(),
			);
		}
		$fp->js('change')->univ()->location($js_arr);
		$fq->js('change')->univ()->location($js_arr);
		$fr->js('change')->univ()->location($js_arr);
		
        $v=$this->add('View')->setClass('clear');
    }
}
