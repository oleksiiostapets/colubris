<?php
class View_Report extends View {
	public $m;
    function init(){
        parent::init();

	    $this->prepareModel();

	    //Total spent time
	    $total=0;
	    foreach($this->m->getRows() as $r){
		    $total+=$r['spent_time'];
	    }
	    $vv = $this->add('View')->addStyle('overflow','hidden')->addClass('atk-push-small');

	    $v=$vv->add('View');
        $v->setClass('atk-move-left');
        $v->setHtml('<p style="font-weight:bold;font-size:18px;">TOTAL SPENT TIME: '.$total.'</p>');

	    //Add XLS button
        $v=$vv->add('View');
        $v->setClass('atk-move-right');
	    $this->addXSLButton($v);

	    //Add Grid
        $this->addReports();

        $this->add('View')->setHtml('<p style="font-weight:bold;font-size:18px;">TOTAL SPENT TIME: '.$total.'</p>');
    }
	protected function addReports(){
		$cr=$this->add('Grid');
		$cr->addClass('zebra bordered');

		$cr->setModel($this->m,$this->grid_show_fields);
		$cr->setFormatter('task_name','wrap');

		$cr->addTotals(array('spent_time'));

		$cr->addPaginator(50);
		$cr->addColumn('button','more');

		if( $_GET['more'] ) {
			$this->js()->univ()->frameURL('More',$this->api->url('./more',array('task_time_id' => $_GET['more'])))->execute();
		}
	}
	protected function addXSLButton($v){
		$properties = array(
			'creator'        => 'Oleksii Ostapets',
			'lastModifiedBy' => 'Oleksii Ostapets',
			'title'          => 'Colubris report',
			'subject'        => 'Colubris report',
			'description'    => 'Colubris report',
			'keywords'       => 'Colubris report',
			'category'       => 'Colubris report'
		);
		$fields = array('project_name', 'task_name', 'status', 'type', 'spent_time','date','user');
		$fields_width = array(15, 30, 10, 14, 12, 15);
		$count_totals = array('spent_time');
		$v->add('KonstantinKolodnitsky/kk_xls/View_ButtonXLS',array(
			'data'         => $this->m,
			'properties'   => $properties,
			'fields'       => $fields,
			'fields_width' => $fields_width,
			'count_totals' => $count_totals
		))->set('Export to XLS');
	}
	protected function prepareModel(){
		$this->m=$this->add('Model_TaskTime');//->debug();
		$this->m->getField('user_id')->caption('Performer');
		$this->m->getField('spent_time')->caption('Spent');
		$this->m->addCondition('spent_time','>','0');
		if($_GET['including']==1){
			$this->m->addCondition('remove_billing',false);
		}elseif($_GET['including']==2){
			$this->m->addCondition('remove_billing',true);
		}

		$j_task = $this->m->join('task.id','task_id','left','_t');
		$j_task->addField('task_name','name');
		$j_task->addField('status','status');
		$j_task->addField('type','type');
//        $j_task->addField('estimate','estimate');
		$j_task->addField('project_id','project_id');
		$j_task->addField('organisation_id','organisation_id');

		$j_project = $j_task->join('project.id','project_id','left','_p');
		$f1=$j_project->addField('project_name','name');
		$f1->sortable(true);

		$j_req = $j_task->join('requirement','requirement_id','left','_req');
		$j_req->addField('quote_id','quote_id');

		$this->m->addCondition('organisation_id',$this->api->auth->model['organisation_id']);

		if( ($this->api->currentUser()->isDeveloper()) || $this->api->currentUser()->isClient() ){
			$mp=$this->add('Model_Project')->notDeleted();
			if($this->api->currentUser()->isDeveloper()) $projects=$mp->forDeveloper();
			if($this->api->currentUser()->isClient()) $projects=$mp->forClient();
			$projects_ids="";
			foreach($projects->getRows() as $p){
				if($projects_ids=="") $projects_ids=$p['id'];
				else $projects_ids=$projects_ids.','.$p['id'];
			}
			$this->m->addCondition('project_id','in',$projects_ids);
		}

		if($this->api->recall('project_id')>0){
			$this->m->addCondition('project_id',$this->api->recall('project_id'));
		}
		if($this->api->recall('quote_id')>0){
			$check_quote=$this->add('Model_Quote')->notDeleted()->getThisOrganisation()->tryLoad($this->api->recall('quote_id'));
			if($check_quote->loaded()){
				if($check_quote->get('project_id')==$this->api->recall('project_id')){
					$this->m->addCondition('quote_id',$this->api->recall('quote_id'));
				}
			}
		}
		if($this->api->recall('quote_id')==-1){
			$this->m->addCondition('quote_id','>',0);
		}
		if($this->api->recall('quote_id')==-2){
			$this->m->addCondition('quote_id',null);
		}
		if($this->api->recall('performer_id')>0){
			$this->m->addCondition('user_id',$this->api->recall('performer_id'));
		}
		if($this->api->recall('date_from')!=''){
			$date=date('Y-m-d',strtotime(str_replace('/','-',$this->api->recall('date_from'))));
			$this->m->addCondition('date','>=',$date);
		}
		if($this->api->recall('date_to')!=''){
			$date=date('Y-m-d',strtotime(str_replace('/','-',$this->api->recall('date_to'))));
			$this->m->addCondition('date','<=',$date);
		}


	}
}