<?php
class View_Switcher extends View {
    public $quotes;
    function init(){
        parent::init();

        $v=$this->add('View')->setClass('right');
        
        $f=$v->add('Form');
        $f->addClass('horizontal');
        // Project
        $mp=$this->add('Model_Project_Participant');

        if($_GET['project_id']){
        	$this->api->memorize('project_id',$_GET['project_id']);
        	$this->api->memorize('quote_id',0);
        	$this->api->memorize('requirement_id',0);
        }elseif(!$this->api->recall('project_id')){
        	if($mp->count()>0){
        		$projects=$mp->getRows();
        		$this->api->memorize('project_id',$projects[0]['id']);
        	}
        }
        $fp=$f->addField('dropdown','project');
        $fp->setModel($mp);
        $fp->set($this->api->recall('project_id'));
        $fp->js('change',$fp->js()->colubris()->myredirect($fp->name,'project_id',$this->api->url()));
        
        // Quote
		$mq=$this->add('Model_Quote');
		$mq->addCondition('status','estimation_approved');
		$mq->addCondition('project_id',$this->api->recall('project_id'));
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
		$fq->js('change',$fq->js()->colubris()->myredirect($fq->name,'quote_id',$this->api->url()));
		
		// Requirement
		$mr=$this->add('Model_Requirement');
		$mr->addCondition('quote_id',$this->api->recall('quote_id'));
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
		$fr->js('change',$fr->js()->colubris()->myredirect($fr->name,'requirement_id',$this->api->url()));
		
		// Status
		$s_arr=array_merge(array('all'=>'all'),$this->api->task_statuses);
		if($_GET['status']!==null){
			$this->api->memorize('status',$_GET['status']);
		}else{
			$this->api->memorize('status','all');
		}
		$fs=$f->addField('dropdown','status');
		$fs->setValueList($s_arr);
		$fs->set($this->api->recall('status'));
		$fs->js('change',$fs->js()->colubris()->myredirect($fs->name,'status',$this->api->url()));
		
		// Assigned_to
		$ma=$this->add('Model_User')->setOrder('name');
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
		$fa->js('change',$fa->js()->colubris()->myredirect($fa->name,'assigned_id',$this->api->url()));
				
        $v=$this->add('View')->setClass('clear');
    }
}
