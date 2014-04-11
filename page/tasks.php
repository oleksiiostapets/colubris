<?php
class page_tasks extends Page_Functional {
    function page_index(){

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->app->user_access->canSeeTaskList() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }

        $this->addFilter();
        $this->stickeGetFilterVars();
        $this->addBC();
        $crud = $this->addTaskCRUD();

        $this->filter->addViewToReload($crud);
        $this->filter->commit();
    }
    private function addBC() {
        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Tasks',
                    'url' => 'tasks',
                ),
            )
        ));
   }
    private function addTaskCRUD() {
        $m = $this->add('Model_Task');
        $m->Base();
        $m->addQuoteName();
        $m->addGetConditions();

        $cr = $this->add('CRUD_Task',array(
            'form_class' => 'Form_EditTask',
            'grid_class' => 'Grid_Tasks',
            'allow_add'  => true,
            'allow_edit' => true,
            'allow_del'  => true
        ));
        $cr->setModel($m,
            $this->app->user_access->whatTaskFieldsUserCanEdit(),
            $this->app->user_access->whatTaskFieldsUserCanSee()
        );
        $cr->configure();
        return $cr;
    }
}
