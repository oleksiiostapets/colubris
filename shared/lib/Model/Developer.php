<?php
class Model_Developer extends Model_User {
    function init(){
        parent::init();

        $this->addCondition('is_developer',true);
        $this->addCondition('is_deleted',false);

		$this->getUsersOfOrganisation();

        //$this->addField('timesheets_tw')->calculated(true);
        //$this->addField('reports_tw')->calculated(true);
        //$this->addField('weekly_target')->datatype('int');
    }

    function reportsDQ(){
        return $this->add('Model_Timesheet')->dsql()
            ->where('T.user_id=u.id')
            ;
    }
    function getTimesheets(){
        return $this->add('Model_Timesheet')
            ->addCondition('user_id',$this->get('id'));
    }
    function timesheetsDQ(){
        return $this->add('Model_Timesheet')->dsql()
            ->where('user_id=u.id')
            ;
    }
    function calculate_timesheets_tw(){
        return 

            'round(('.
                        $this->timesheetsDQ()
                        ->field('sum(minutes)')
                        ->where('date>date(DATE_ADD(now(), INTERVAL(2-DAYOFWEEK(now())) DAY))')
                        ->select()
                        .')/60,2)';
    }
    function calculate_reports_tw(){
        return 

            'round(('.
                        $this->reportsDQ()
                        ->field('sum(minutes)')
                        ->where('date>date(DATE_ADD(now(), INTERVAL(2-DAYOFWEEK(now())) DAY))')
                        ->select()
                        .')/60,2)';
    }

    // API methods
    function prepareForSelect(Model_User $u){
        $r = $this->add('Model_User_Right');

        $fields = ['id'];

        if($r->canSeeDevelopers($u['id'])){
            $fields = array('id','email','name','password','is_admin','is_manager','is_financial','is_developer','is_sales','hash','mail_task_changes','is_deleted','deleted_id','avatar_id','deleted','is_system','client_id','client','chash','organisation_id','organisation','lhash','lhash_exp','is_client','avatar','avatar_thumb');
        }

        $this->setActualFields($fields);
        return $this;
    }
    function prepareForInsert(Model_User $u){
        $r = $this->add('Model_User_Right');

        if($r->canManageDevelopers($u['id'])){
            $fields = array('id','email','name','password','is_admin','is_manager','is_financial','is_developer','is_sales','hash','mail_task_changes','is_deleted','deleted_id','avatar_id','deleted','is_system','client_id','client','chash','organisation_id','organisation','lhash','lhash_exp','is_client','avatar','avatar_thumb');
        }else{
            throw $this->exception('This User cannon add record','API_CannotAdd');
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
        $r = $this->add('Model_User_Right');

        if($r->canManageDevelopers($u['id'])){
            $fields = array('id','email','name','password','is_admin','is_manager','is_financial','is_developer','is_sales','hash','mail_task_changes','is_deleted','deleted_id','avatar_id','deleted','is_system','client_id','client','chash','organisation_id','organisation','lhash','lhash_exp','is_client','avatar','avatar_thumb');
        }else{
            throw $this->exception('This User cannon edit record','API_CannotAdd');
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
        $r = $this->add('Model_User_Right');

        if($r->canManageDevelopers($u['id'])) return $this;

        throw $this->exception('This user has no permissions for deleting','API_CannotDelete');
    }
}
