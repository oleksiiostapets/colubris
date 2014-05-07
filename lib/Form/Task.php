<?php
/**
 *Created by Konstantin Kolodnitsky
 * Date: 06.05.14
 * Time: 19:55
 */
class Form_Task extends Form{
	public $model;
	function init(){
		parent::init();

		$this->setModel($this->model,array(
			'name',
			'descr_original',
//			'project_id',
			'priority',
			'type',
			'status',
			'estimate',
			'requester_id',
			'assigned_id'
		));

		$this->addRequirement();
		$this->addQuote();
		$this->addProject();

//		$this->order();

		$this->addSubmit('Save');
		if($this->isSubmitted()){
			/*if($_GET['edit_quote_id']>0 && $_GET['edit_requirement_id']==0){
				$this->js()->univ()->alert('You must select Requirement!')->execute();
				return;
			}*/
//			$this->update();
			$this->js()->univ()->successMessage('Successfully updated task details')->execute();
		}
	}
	function addProject(){
		$project_model = $this->add('Model_Project')->notDeleted();
		$project_model->forRole($this->app->getCurrentUserRole());

		$project = $this->addField('DropDown','project');
		$project->setModel($project_model,array('name'));

		if ($g = $_GET['project']) {
			$project->set($g);
		}

		// reload on change
		$project->selectnemu_options = array(
			'change' => $this->js(null,'
                function() {'.
//					$this->js()->colubris()->reloadForm($this->name,'quote')
//					$_GET['quote'] = $project->js()->val()
					$this->js()->reloadField($this->getElement('quote'),null,null,$project->js()->val())
//					$this->js()->univ()->alert($project->js()->val())
					.'}'
				)
		);
	}
	private function addQuote(){
		$quote = $this->addField('DropDown','quote');
		$quote->setEmptyText('Select a quote');
		if($_GET['quote'] || $this->model->get('quote_id')){//TODO temporary
			$m = $this->add('Model_Quote')->notDeleted();
			$m->addCondition('project_id',$this->model->get('project_id'));
			$quote->setModel($m,array('name'));
			$quote->set($this->model->get('quote_id'));//TODO change to $_GET['quote']
		}

		// reload on change
		/*$quote->selectnemu_options = array(
			'change' => $this->js(null,'
                function() {'.
					$this->js()->colubris()->reloadForm($this->name,'quote')
					.'}'
				)
		);*/
	}
	private function addRequirement(){
		$quote = $this->addField('DropDown','requirement');
		$quote->setEmptyText('Select a requirement');
		if ($_GET['project'] && $g = $this->model->get('quote_id')) {
			$m = $this->add('Model_Requirement')->notDeleted();
			$m->addCondition('quote_id',$g);
			$quote->setModel($m,array('name'));
			$quote->set($this->model['requirement_id']);
		}
	}
	private function order(){
		$this->add('Order')
			->move('project','after','descr_original')
			->move('quote','after','project')
			->move('requirement','after','quote')
			->now();
	}
}