<?php
class View_TasksCRUD extends View {
    function init(){
        parent::init();

        $m = $this->add('Model_Task');

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
    }
}
