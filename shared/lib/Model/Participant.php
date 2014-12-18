<?php
class Model_Participant extends Model_Table {
    public $table='participant';
    private $right;

    function init(){
        parent::init();

		$mu = $this->add('Model_User')->getUsersOfOrganisation();
        $this->hasOne($mu,'user_id');
        $this->getField('user_id')->mandatory(true);

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

        $this->right = $this->add('Model_User_Right');
    }

    /**
     *
     * API methods
     *
     */
    function prepareForSelect(Model_User $u){
        $fields = ['id'];

        if($this->right->canManageParticipants($u['id'])){
            $fields = array('id','user_id','user','project_id','project','role','hourly_rate');
        }else{
            throw $this->exception('This User cannot see projects','API_CannotSee');
        }

        $this->setActualFields($fields);
        return $this;
    }
    function prepareForInsert(Model_User $u){
        $fields = ['id'];

        if($this->right->canManageParticipants($u['id'])){
            $fields = array('id','user_id','project_id','role','hourly_rate');
        }else{
            throw $this->exception('This User cannot add projects','API_CannotAdd');
        }

        foreach ($this->getActualFields() as $f){
            $fo = $this->hasElement($f);
            if(in_array($f, $fields)){
                if($fo) $fo->editable = true;
            }else{
                if($fo) $fo->editable = false;
            }
        }
        return $this;
    }
    function prepareForUpdate(Model_User $u){
        $fields = ['id'];

        if($this->right->canManageParticipants($u['id'])){
            $fields = array('id','user_id','project_id','role','hourly_rate');
        }else{
            throw $this->exception('This User cannon edit quotes','API_CannotEdit');
        }

        foreach ($this->getActualFields() as $f){
            $fo = $this->hasElement($f);
            if(in_array($f, $fields)){
                if($fo) $fo->editable = true;
            }else{
                if($fo) $fo->editable = false;
            }
        }
        return $this;
    }
    function prepareForDelete(Model_User $u){
        if($this->right->canManageParticipants($u['id'])) return $this;
        throw $this->exception('This user has no permissions for deleting','API_CannotDelete');
    }
}