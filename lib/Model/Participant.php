<?php
class Model_Participant extends Model_Table {
    public $table='participant';

    function init(){
        parent::init();

		$this->addField('user_id')
            ->mandatory(true)
            ->refModel('Model_User_Organisation');

        $this->addField('project_id')
            ->mandatory(true)
            ->refModel('Model_Project');

        $this->addfield('role')
            ->type('list')->listData(array(
                    'manager'=>'Manager',
                    'developer'=>'Developer',
                    'qa'=>'Quality Assurance',
                    'design'=>'Designer',
                    ));


	}
}
