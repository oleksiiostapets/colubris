<?php
class View_SwitcherQuotes extends View {
    function init(){
        parent::init();

        $this->api->stickyGet('project_id');
        $this->api->stickyGet('client_id');

        if($_GET['project_id']>0){
            $_GET['client_id']=0;
            $this->api->memorize('q_project_id',$_GET['project_id']);
            $this->api->memorize('q_client_id',0);
        }elseif($_GET['project_id']==0){
            $this->api->memorize('q_project_id',0);
        }
        if($_GET['client_id']>0){
            $_GET['project_id']=0;
            $this->api->memorize('q_project_id',0);
            $this->api->memorize('q_client_id',$_GET['client_id']);
        }elseif($_GET['client_id']==0){
            $this->api->memorize('q_client_id',0);
        }

        isset($this->class)?$class=$this->class:$class='right';
        $v=$this->add('View')->setClass($class);
        
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
        $p_arr=array();
        $p_arr['0']='all';
        foreach ($projects as $p){
        	$p_arr[$p['id']]=$p['name'];
        }
        $fp=$f->addField('dropdown','project');
        $fp->setValueList($p_arr);
        $_GET['project_id']=$this->api->recall('q_project_id');
        $fp->set($this->api->recall('q_project_id'));

        // Client
        if(!$this->api->currentUser()->isClient()){
            $mc=$this->add('Model_Client');
            if($this->api->currentUser()->isDeveloper()){
                $mc=$mc->forDeveloper();
            }
            $clients=$mc->getRows();
            $c_arr=array();
            $c_arr['0']='all';
            foreach ($clients as $c){
                $c_arr[$c['id']]=$c['name'];
            }
            $fc=$f->addField('dropdown','client');
            $fc->setValueList($c_arr);
            $_GET['client_id']=$this->api->recall('q_client_id');
            $fc->set($this->api->recall('q_client_id'));
        }

		$fp->js('change')->univ()->location(
            array(
                $this->api->url(),
                'project_id'=>$fp->js()->val(),
                'client_id'=>0,
            )
        );
		$fc->js('change')->univ()->location(
            array(
                $this->api->url(),
                'project_id'=>0,
                'client_id'=>$fc->js()->val(),
            )
        );

        $v=$this->add('View')->setClass('clear');
    }
}
