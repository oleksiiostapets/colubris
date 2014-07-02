<?php
/**
 *Created by Konstantin Kolodnitsky
 * Date: 23.04.14
 * Time: 14:23
 */
class CRUD_Requirement extends CRUD{
	public $form_class = 'Form_EditTask';
	public $grid_class = 'Grid_Tasks';
	public $items_per_page = 25;
	function init(){
		parent::init();
	}
	function configure() {
		$this->addMoreFrame();
	}
	protected function configureGrid($fields) {
		parent::configureGrid($fields);
		if($g = $this->grid){
			$g->addClass('zebra bordered');
			$g->addPaginator($this->items_per_page);
			$g->addFormatter('file','download');
			$g->setFormatter('name','wrap');

		}
	}
	public $quote;
	public $req;
	protected function addMoreFrame() {
		if($p = $this->addFrame('More')){
			if (!$this->id) {
				throw $this->exception('requirement_id must be provided!');
			}
			$this->req=$this->add('Model_Requirement')->notDeleted()->load($this->id);
			$this->quote=$this->add('Model_Quote')->notDeleted()->getThisOrganisation()->load($this->req->get('quote_id'));
			$_GET['project_id']=$this->quote->get('project_id');

			$v = $p->add('View');

			$v->add('H1')->set($this->req->get('name'));

			$this->addDetails($v);
			$this->addComments($v);
			$this->addTasks($v);
		}
	}
	protected function addTasks($v){
		$this->tasks = $this->add('Model_Task')->restrictedUsers();
		$this->tasks->addCondition('project_id',$this->req['project_id']);
		$this->tasks->addCondition('requirement_id',$this->req['id']);

		$v->add('H3')->set('Tasks:');
		if (!$this->quote->isExpired()){
			$allow_add=true;
			$can_edit=true;
			$can_del=true;
		}else{
			$allow_add=false;
			$can_edit=false;
			$can_del=false;
		}
		$cr = $v->add('CRUD', array(
			'grid_class'      => 'Grid_Tasks',
			'allow_add'       => $allow_add,
			'allow_edit'      => $can_edit,
			'allow_del'       => $can_del,
		));

		if ($cr->grid) {
			$cr->grid->quote = $this->quote;
		}

		$cr->setModel(
			$this->tasks,
			$this->app->user_access->whatTaskFieldsUserCanEdit(),
			$this->app->user_access->whatTaskFieldsUserCanSee($this->quote)
		);
	}
	protected function addComments($v){
		$v->add('H3')->set('Comments:');

		$cr=$v->add('CRUD', array('grid_class'=>'Grid_Reqcomments'));

		$m=$this->add('Model_Reqcomment')->notDeleted()
			->addCondition('requirement_id',$this->req['id']);
		$cr->setModel($m,
			array('text','file_id'),
			array('text','user','file','file_thumb','created_dts')
		);
		if($cr->grid){
			$cr->grid->addClass('zebra bordered');
			$cr->add_button->setLabel('Add Comment');
//			$cr->grid->setFormatter('text','text');
		}

//		$this->tasks=$this->add('Model_Task')->restrictedUsers();
//		$this->tasks->addCondition('project_id',$this->req['project_id']);
//		$this->tasks->addCondition('requirement_id',$this->req['id']);
//		$this->addTasksCRUD($this);
	}
	protected function addDetails($v){
		$v->add('H4')->set('Details:');

		$source = array(
			array(
				'name'=>'Project Name',
				'value'=>$this->quote->get('project'),
			),
			array(
				'name'=>'Quote Name',
				'value'=>$this->quote->get('name'),
			),
		);
		$show_fields=$this->quote->whatRequirementFieldsUserCanSee($this->api->currentUser());
		foreach($show_fields as $field){
			if($field!='name' && $field!='count_comments'){
				if($field=='is_included'){
					$this->req->get($field)==1?$value='Y':$value='N';
				}else{
					$value=$this->req->get($field);
				}
				$source[]=array(
					'name'=>$field,
					'value'=>$value
				);
			}
		}

		$gr = $v->add('Grid_Quote');
		$gr->addColumn('text','name','');
		$gr->addColumn('text','value','Info');
		$gr->setFormatter('value','wrap');
		$gr->addFormatter('value','download');
		$gr->setSource($source);
	}
}