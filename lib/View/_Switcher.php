<?php
class View_Switcher extends View {
    public $fp;
    public $fq;
    public $fr;
    public $f;
    public $fs;
    public $fa;
    function init(){
        parent::init();

        $this->api->stickyGet('project_id');
        $this->api->stickyGet('quote_id');
        $this->api->stickyGet('requirement_id');
        $this->api->stickyGet('status');
        $this->api->stickyGet('assigned_id');

//        isset($this->class)?$class=$this->class:$class='right';
        $v=$this->add('View');//->setClass($class);
        
        $this->f=$v->add('Form');
        $this->f->addClass('atk-form-stacked horizontal');

        $this->addProject();
        $this->addQuote();
        $this->addRequirement();

        if (strpos($this->api->page,'new')===false){
            $this->addStatus();
            $this->addAssigned();

            $js_arr=array(
                $this->api->url(),
                'project_id'=>$this->fp->js()->val(),
                'quote_id'=>$this->fq->js()->val(),
                'requirement_id'=>$this->fr->js()->val(),
                'status'=>$this->fs->js()->val(),
                'assigned_id'=>$this->fa->js()->val(),
            );

            $this->fs->js('change')->univ()->location($js_arr);
            $this->fa->js('change')->univ()->location($js_arr);
        }else{
            $js_arr=array(
                $this->api->url(),
                'project_id'=>$this->fp->js()->val(),
                'quote_id'=>$this->fq->js()->val(),
                'requirement_id'=>$this->fr->js()->val(),
            );
        }

//		$this->fp->js('change')->univ()->location($js_arr);
		$this->fp->js('change')->univ()->location($this->api->url('quotes'));
		$this->fq->js('change')->univ()->location($js_arr);
		$this->fr->js('change')->univ()->location($js_arr);
		
        $v=$this->add('View')->setClass('clear');
    }
    function addAssigned(){
        $ma=$this->add('Model_User_Task')->setOrder('name');
        $a_arr=$ma->getRows();
        $u_arr['0']='all';
        foreach($a_arr as $a){
            $u_arr[$a['id']]=$a['name'];
        }
        if($_GET['assigned_id']!==null){
            $this->api->memorize('assigned_id',$_GET['assigned_id']);
        }
        $this->fa=$this->f->addField('dropdown','assigned_id','Assigned');
        $this->fa->setValueList($u_arr);
        $this->fa->set($this->api->recall('assigned_id'));
    }
    function addStatus(){
        $s_arr=array_merge(array('all'=>'all'),$this->api->task_statuses);
        if($_GET['status']!==null){
            $this->api->memorize('status',$_GET['status']);
        }else{
            if (is_null($this->api->recall('status'))) {
                $this->api->memorize('status','all');
            }
        }
        $this->fs=$this->f->addField('dropdown','status');
        $this->fs->setValueList($s_arr);
        $this->fs->set($this->api->recall('status'));
    }
    function addProject(){
        $mp=$this->add('Model_Project');
        if($this->api->currentUser()->isDeveloper()){
            $mp=$mp->forDeveloper();
        }elseif($this->api->currentUser()->isClient()){
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
        $this->fp=$this->f->addField('dropdown','project');
        $this->fp->setValueList($p_arr);
        $_GET['project_id']=$this->api->recall('project_id');
        $this->fp->set($this->api->recall('project_id'));
    }
    function addRequirement(){
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
        $this->fr=$this->f->addField('dropdown','requirement');
        $this->fr->setValueList($rn_arr);
        $this->fr->set($this->api->recall('requirement_id'));
    }
    function addQuote(){
        // Quote
        $mq=$this->add('Model_Quote')->notDeleted()->getThisOrganisation();
//		$mq->addCondition('status','estimation_approved');
        //$mq->addCondition('status','IN',array('estimation_approved','estimated'));
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
        $this->fq=$this->f->addField('dropdown','quote');
        $this->fq->setValueList($qn_arr);
        $this->fq->set($this->api->recall('quote_id'));
    }
}
