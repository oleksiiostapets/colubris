<?php
class Model_Participant extends Model_Table {
    public $table='participant';

    function init(){
        parent::init();

//		$this->addField('user_id')
//            ->mandatory(true)
//            ->refModel('Model_User_Organisation');
		$mu = $this->add('Model_User')->getUsersOfOrganisation();
        $this->hasOne($mu,'user_id');
        $this->getField('user_id')->mandatory(true);

//        $this->addField('project_id')
//            ->mandatory(true)
//            ->refModel('Model_Project');
        $this->hasOne('Project','project_id');
        $this->getField('project_id')->mandatory(true);

        $this->addfield('role')
            ->type('list')->listData(array(
                    'manager'=>'Manager',
                    'developer'=>'Developer',
                    'qa'=>'Quality Assurance',
                    'design'=>'Designer',
                    ));

        $this->addField('hourly_rate');

    }
}
