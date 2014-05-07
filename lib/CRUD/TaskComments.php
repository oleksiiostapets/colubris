<?php
/**
 *Created by Konstantin Kolodnitsky
 * Date: 07.05.14
 * Time: 13:15
 */
class CRUd_TaskComments extends CRUD{
	public $grid_class = 'Grid_Reqcomments';
	function init(){
		parent::init();

		$m = $this->add('Model_Taskcomment')->notDeleted()
			->addCondition('task_id',$this->task_id);
		$this->setModel($m,
			array('text','file_id'),
			array('text','user','user_id','file','file_thumb','created_dts')
		);

		if ($this->add_button) {
			$this->add_button->setLabel('Add Comment');
		}
		if($this->grid){
			$this->grid->addClass('zebra bordered');
			$this->grid->setFormatter('text','text');
			$this->grid->addFormatter('text','wrap');
			$this->grid->addPaginator(10);
		}
		if($_GET['delete']){
			$comment=$this->add('Model_Taskcomment')->notDeleted()->load($_GET['delete']);
			$comment->delete();
			$this->js()->reload()->execute();
		}
	}
}