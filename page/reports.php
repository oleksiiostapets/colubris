<?php
class page_reports extends Page_Functional {
	public $m;
    function page_index(){

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->app->model_user_rights->canSeeReports() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }

        $this->title = 'Reports';

	    $this->m=$this->add('Model_TaskTime');
	    $this->m->forReportsGrid();

	    $this->addFilter();
	    $this->stickeGetFilterVars();
	    $this->addBC();

	    //Add XLS button
//	    $v=$this->add('View');
//	    $v->setClass('atk-move-right');
//	    $this->addXSLButton($v);


	    //Total spent time
	    $total=0;
	    foreach($this->m->getRows() as $r){
		    $total+=$r['spent_time'];
	    }
	    $vv = $this->add('View')->addStyle('overflow','hidden')->addClass('atk-push-small');

	    $v=$vv->add('View');
	    $v->setClass('atk-move-left');
	    $v->setHtml('<p style="font-weight:bold;font-size:18px;">TOTAL SPENT TIME: '.$total.'</p>');
	    $this->filter->addViewToReload($v);

	    $grid = $this->addReportsGrid();

	    $v = $this->add('View')->setHtml('<p style="font-weight:bold;font-size:18px;">TOTAL SPENT TIME: '.$total.'</p>');
	    $this->filter->addViewToReload($v);

	    $this->filter->addViewToReload($grid);
	    $this->filter->commit();
    }
	protected function addFilter() {
		$this->filter = $this->app->add('Controller_Filter');
		$filter_form = $this->add('Form_Filter_WithDate');
		$this->filter->setForm($filter_form);
		$this->filter->addViewToReload($filter_form);
	}
	private function addReportsGrid(){

		$cr=$this->add('Grid');
		$cr->addClass('zebra bordered');

		$cr->setModel($this->m,$this->getGridFields());
		$cr->setFormatter('task_name','wrap');

		$cr->addTotals(array('spent_time'));

		$cr->addPaginator(50);
		$cr->addColumn('button','more');

		if( $_GET['more'] ) {
			$this->js()->univ()->frameURL('More',$this->api->url('./more',array('task_time_id' => $_GET['more'])))->execute();
		}

		return $cr;
	}
	private function addBC() {
		$this->add('x_bread_crumb/View_BC',array(
			'routes' => array(
				0 => array(
					'name' => 'Home',
				),
				1 => array(
					'name' => 'Reports',
					'url' => 'reports',
				),
			)
		));
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
		$fields = $this->getExportFields();
		$count_totals = array('spent_time');
		$v->add('KonstantinKolodnitsky/kk_xls/View_ButtonXLS',array(
			'data'         => $this->m,
			'properties'   => $properties,
			'fields'       => $fields['fields'],
			'fields_width' => $fields['fields_width'],
			'count_totals' => $count_totals
		))->set('Export to XLS');
	}
    function getGridFields() {
        return array('project_name','quote_name','requirement_name','task_name','status','type','spent_time','date','user');
    }
    function getExportFields() {
        return [
	        'fields'       => ['project_name','task_name','status','type','spent_time','date'],
	        'fields_width' => [15, 30, 10, 14, 12, 15]
        ];
    }
    function page_more(){
        if (!$_GET['task_time_id']) {
            throw $this->exception('task_time_id must be provided!');
        }
        $this->api->stickyGET('task_time_id');
        $task_time=$this->add('Model_TaskTime')->load($_GET['task_time_id']);
        $task=$this->add('Model_Task')->load($task_time['task_id']);


        $v = $this->add('View');

        // Description
        $descr_view = $v->add('View')->addClass('span12');
        $descr_view->add('H4')->set('Description');
        $descr_view->add('View')->setHtml( $this->api->colubris->makeUrls(nl2br($task->get('descr_original'))) );

    }
    function defaultTemplate() {
        return array('page/reports');
    }
}