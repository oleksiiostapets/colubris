<?
class page_admin_screens extends Page_EntityManager {
	public $controller='Controller_Screen';

	function initMainPage(){
		parent::initMainPage();

		$this->grid->addColumnPlain('expander','tasks','Tasks');
	}
	function page_tasks(){
		// This implements a quite complete task editing solution. 

		// Preserve screen_id throughout the whole thing
		$this->api->stickyGET('id');

		// Create form for editing (or adding) task
		$c=$this->add('Controller_Task');
		$f=$this->add('Form')->setController($c);
		// set default values for this form
		$f->set('screen_id',$_GET['id']);
		$f->set('budget_id',$this->add('Controller_Screen')->loadData($_GET['id'])->get('budget_id'));
		// If task_id is specified, then keep it sticky and load for editing
		if($_GET['task_id']){
			$this->api->stickyGET('task_id');
			$c->loadData($_GET['task_id']);
		}

		// Create list of tasks for this screen
		$c=$this->add('Controller_Task');$c->addCondition('screen_id',$_GET['id']);
		$g=$this->add('Grid')->setController($c);

		// Add 2 extra columns for editing and deletion
		$g->addColumnPlain('button','edit');
		$g->addColumnPlain('delete','delete');

		// If editing buton is clicked, reload form with task_id argument
		if($_GET['edit']){
			$f->js()->reload(array('task_id'=>$_GET['edit']))->execute();
		}

		if($f->isSubmitted()){
			// If our main form is submitted, then save data and relad both the grid and the form
			$f->update();
			$f->js()->closest('.atk4_loader')->atk4_loader('reload')->execute();
			//$f->js(null,$g->js()->reload())->reload(array('task_id'=>null))->execute();

		}

	}
}
