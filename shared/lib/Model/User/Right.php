<?php
/**
 *Created by Konstantin Kolodnitsky
 * Date: 11.07.14
 * Time: 11:38
 *
 * HOW TO!
 * You can:
 * - check right:
 *     - canSeeTasks() or;
 *     - getRights($user_id).
 * - set rights for new user:
 *     - saveNewUserAsDeveloper() as a pattern or;
 *     - setRight(<right_name>,[true||false]) for individual right.
 * - set rights for existing user:
 *     - make_existing_user_as_developer();
 *     - setRight(<right_name>,[true||false]) for individual right.
 * - switch right:
 *     - toggle_right($right_name).
 * You CANNOT use set()
 */
class Model_User_Right extends SQL_Model{
    public $table = 'right';
    private $user;
    static $available_rights = array(
        //task
        'can_see_tasks',
        'can_add_task',
        'can_edit_task',
        'can_delete_task',
        'can_add_comment_to_task',
        //Project
        'can_see_projects',
        'can_add_projects',
        'can_edit_projects',
        'can_delete_projects',
        'can_manage_participants',
        //Quote
        'can_see_quotes',
        'can_add_quote',
        //Request for quotation
        'can_edit_quote',
        'can_delete_quote',
        'can_submit_for_quotation',
        //Requirement
        'can_add_requirement',
        'can_edit_requirement',
        'can_delete_requirement',
        'can_add_comment_to_requirement',
        //Setting
        'can_see_settings',
        'can_edit_settings',
        //User
        'can_see_users',
        'can_manage_users',
        //Rates
        'can_see_rates',
        'can_manage_rates',
        //Developers
        'can_see_developers',
        'can_manage_developers',
        //Clients
        'can_see_clients',
        'can_manage_clients',
        //Reports
        'can_see_reports',
        //Deleted Items
        'can_see_deleted',
        'can_restore_deleted',
        //Logs
        'can_see_logs',
        //Mics
        'can_see_dashboard',
        'can_move_to_from_archive',
        'can_track_time',
        'can_login_as_any_user',
        'can_see_time',
        'can_see_finance',
        'can_see_spent_time',
        'can_see_manager',
        'can_manage_all_records'
    );
    private $developer_pattern = 'can_see_tasks,can_add_task,can_edit_task,can_delete_task,can_add_comment_to_task,can_see_projects,can_see_quotes,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_reports,can_see_dashboard,can_move_to_from_archive,can_track_time,can_manage_all_records';
    private $client_pattern = 'can_see_tasks,can_add_task,can_delete_task,can_add_comment_to_task,can_see_projects,can_see_quotes,can_add_projects,can_add_quote,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_dashboard';
    private $sales_manager_pattern = 'can_see_quotes,can_add_quote,can_add_requirement,can_see_settings,can_edit_settings,can_see_dashboard,can_move_to_from_archive';
    private $manager_pattern = 'can_see_tasks,can_add_task,can_edit_task,can_delete_task,can_add_comment_to_task,can_see_projects,can_see_quotes,can_add_projects,can_edit_projects,can_delete_projects,can_add_quote,can_edit_quote,can_delete_quote,can_submit_for_quotation,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_rates,can_manage_rates,can_see_clients,can_manage_clients,can_see_reports,can_see_deleted,can_restore_deleted,can_see_dashboard,can_move_to_from_archive,can_see_time,can_track_time,can_manage_all_records';
    private $admin_pattern = 'can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_users,can_manage_users,can_see_rates,can_manage_rates,can_see_developers,can_manage_developers,can_see_logs,can_see_dashboard,can_login_as_any_user,can_manage_all_records';

    function init(){
        parent::init();
//        $this->addField('user_id');
        $this->hasOne('User');
        $this->addField('right')->type('text');
    }

    private $_set = false;
    function set($name,$value=UNDEFINED) {
//        if( !isset($this->app->is_test_app) && !$this->app->canManageUsers() ){
//            throw $this->exception('You cannot edit users\' rights');
//        }
        if ($this->_set) {
            parent::set($name,$value);
            return $this;
        }
        throw $this->exception('This method is private in this model');
    }

    function setRights($user_id = null, $rights = null){
//        if(!$user_i){
//            throw $this->exception('You didn\'t specified user_id for setRights() method//;
//      //}
        if(!$rights){
            throw $this->exception('You didn\'t specified necessary arguments for setRights() method');
        }
        if($this->loaded()){
            $this->unload();
        }
        if($user_id){
            $this->tryLoadBy('user_id', $user_id);
        }
        $new_arr= array();
        foreach($rights as $right){
            if ($this->checkRight($right)){
                $new_arr[] = $right;
            }
        }

        $new_str = implode(',',$new_arr);
        $this->_set = true;
        $this->set('right', $new_str);
        $this->set('user_id', $user_id);
        $this->save();
        return $this->get();
//        $this->set(arr//(
//            'right'   => $new_s//,
//            'user_id' => $rights['user_i//]
//        ))->save//;
        $this->_set = false;
        return $this;
    }
    function setRight($what,$can=true) {
        if ($this->checkRight($what)) {
            $curr = $this->get('right');
            $curr_arr = explode(',',$curr);
            if ($can) {
                $already_exist = false;
                foreach ($curr_arr as $k=>$v) {
                    if ($v == $what) {
                        $already_exist = true;
                    }
                }
                if (!$already_exist) {
                    $arr[] = $what;
                }
                $curr_arr[] = $what;
            } else {
                $new_arr = array();
                foreach ($curr_arr as $k=>$v) {
                    if ($v != $what) {
                        $new_arr[] = $v;
                    }
                }
                $curr_arr = implode(',',$new_arr);
            }
            $this->_set = true;
            $this->set('right',trim(implode(',',$curr_arr),','))->saveLater();
            $this->_set = false;
            return $this;
        }else{
            throw $this->exception('There is no such an access right defined');
        }
    }
    private function checkRight($what) {
        if (in_array($what,self::$available_rights)) {
            return true;
        }
        return false;
    }

    /*
     * Set rights for new user
     */
    private function setPatternForNew($id,$pattern){
        $this->tryLoadBy('user_id',$id);
        if($this->loaded()){
            throw $this->exception('This user exists and already has his rights saved. Use make_as_() to change them');
        }else{
            $this->_set = true;
            $this->set(array(
                'user_id' => $id,
                'right'   => $pattern
            ))->save();
            $this->_set = false;
            return $this;
        }
    }
    public function saveNewUserAsEmpty($id){
        $this->setPatternForNew($id,'');

    }
    public function saveNewUserAsDeveloper($id){
        $this->setPatternForNew($id,$this->developer_pattern);

    }
    public function saveNewUserAsClient($id){
        $this->setPatternForNew($id,$this->client_pattern);
    }
    public function saveNewUserAsSalesManager($id){
        $this->setPatternForNew($id,$this->sales_manager_pattern);
    }
    public function saveNewUserAsManager($id){
        $this->setPatternForNew($id,$this->manager_pattern);
    }
    public function saveNewUserAsAdmin($id){
        $this->setPatternForNew($id,$this->admin_pattern);
    }
    /*
     * Set rights for existing user
     */
    private function setPatternForExisting($id,$pattern){
        $this->tryLoadBy('user_id',$id);
        if($this->loaded()){
            $this->_set = true;
            $this->set('right',$pattern)->save();
            $this->_set = false;
            return $this;
        }else{
            throw $this->exception('There is no rights record for current user. Use "save_new_user_as_()" method to set user rights.');
        }
    }
    public function makeExistingUserAsDeveloper($id){
        $this->setPatternForExisting($id, $this->developer_pattern);
    }
    public function makeExistingUserAsClient($id){
        $this->setPatternForExisting($id, $this->client_pattern);
    }
    public function makeExistingUserAsSalesManager($id){
        $this->setPatternForExisting($id, $this->sales_manager_pattern);
    }
    public function makeExistingUserAsManager($id){
        $this->setPatternForExisting($id, $this->manager_pattern);
    }
    public function makeExistingUserAsAdmin($id){
        $this->setPatternForExisting($id, $this->admin_pattern);
    }



    //////////////
    //Get rights//
    //////////////
    //task
    public function canSeeTasks($id=null)                {return $this->can('can_see_tasks',$id);}
    public function canAddTask($id=null)                 {return $this->can('can_add_task',$id);}
    public function canEditTask($id=null)                {return $this->can('can_edit_task',$id);}
    public function canDeleteTask($id=null)              {return $this->can('can_delete_task',$id);}
    public function canAddCommentToTask($id=null)        {return $this->can('can_add_comment_to_task',$id);}
    //Project
    public function canSeeProjects($id=null)             {return $this->can('can_see_projects',$id);}
    public function canAddProjects($id=null)             {return $this->can('can_add_projects',$id);}
    public function canEditProjects($id=null)            {return $this->can('can_edit_projects',$id);}
    public function canDeleteProjects($id=null)          {return $this->can('can_delete_projects',$id);}
    public function canManageParticipants($id=null)      {return $this->can('can_manage_participants',$id);}
    //Quote
    public function canSeeQuotes($id=null)               {return $this->can('can_see_quotes',$id);}
    public function canAddQuote($id=null)                {return $this->can('can_add_quote',$id);}
    public function canEditQuote($id=null)               {return $this->can('can_edit_quote',$id);}
    public function canDeleteQuote($id=null)             {return $this->can('can_delete_quote',$id);}
    public function canSubmitForQuotation($id=null)      {return $this->can('can_submit_for_quotation',$id);}
    public function canMoveToFromArchive($id=null)       {return $this->can('can_move_to_from_archive',$id);}
    public function canSeeFinance($id=null)              {return $this->can('can_see_finance',$id);}
    public function canSeeSpentTime($id=null)            {return $this->can('can_see_spent_time',$id);}
    //Requirement
    public function canAddRequirement($id=null)          {return $this->can('can_add_requirement',$id);}
    public function canEditRequirement($id=null)         {return $this->can('can_edit_requirement',$id);}
    public function canDeleteRequirement($id=null)       {return $this->can('can_delete_requirement',$id);}
    public function canAddCommentToRequirement($id=null) {return $this->can('can_add_comment_to_requirement',$id);}
    //Setting
    public function canSeeSettings($id=null)             {return $this->can('can_see_settings',$id);}
    public function canEditSettings($id=null)            {return $this->can('can_edit_settings',$id);}
    //User
    public function canSeeUsers($id=null)                {return $this->can('can_see_users',$id);}
    public function canManageUsers($id=null)             {return $this->can('can_manage_users',$id);}
    public function canTrackTime($id=null)               {return $this->can('can_track_time',$id);}
    public function canSeeTime($id=null)                 {return $this->can('can_see_time',$id);}
    //Rates
    public function canSeeRates($id=null)                {return $this->can('can_see_rates',$id);}
    public function canManageRates($id=null)             {return $this->can('can_manage_rates',$id);}
    //Developers
    public function canSeeDevelopers($id=null)           {return $this->can('can_see_developers',$id);}
    public function canManageDevelopers($id=null)        {return $this->can('can_manage_developers',$id);}
    //Clients
    public function canSeeClients($id=null)              {return $this->can('can_see_clients',$id);}
    public function canManageClients($id=null)           {return $this->can('can_manage_clients',$id);}
    //Reports
    public function canSeeReports($id=null)              {return $this->can('can_see_reports',$id);}
    //Deleted Items
    public function canSeeDeleted($id=null)              {return $this->can('can_see_deleted',$id);}
    public function canRestoreDeleted($id=null)          {return $this->can('can_restore_deleted',$id);}
    //Logs
    public function canSeeLogs($id=null)                 {return $this->can('can_see_logs',$id);}
    //Mics
    public function canSeeDashboard($id=null)            {return $this->can('can_see_dashboard',$id);}
    public function canLoginAsAnyUser($id=null)          {return $this->can('can_login_as_any_user',$id);}
    public function canSeeManager($id=null)              {return $this->can('can_see_manager',$id);}
    public function canManageAllRecords($id=null)        {return $this->can('can_manage_all_records',$id);}

    private function fetchRights($rights_string,$right_name){
        $rights_array = explode(',',$rights_string);
        if(in_array($right_name,$rights_array,true)){
            return true;
        }
        return false;
    }
    public function getRights($id=null){
        if (!$id) $id = $this->app->currentUser()->id;
        $this->tryLoadBy('user_id',$id);
        if($this->loaded()){
            return explode(',',$this->get('right'));
        }else{
            return [];
        }

    }
    private function can($right_name,$id=null){
        if(!$this->checkRight($right_name)) throw $this->exception('There is no such an access right defined');
        if(in_array($right_name,$this->getRights($id),true)){
            return true;
        }
        return false;
    }
    /////////////////
    //Toggle rights//
    /////////////////
    public function toggleRight($right){
        if(!$this->checkRight($right)) throw $this->exception('There is no such an access right defined');
        if($this->$right){
            $this->$right = false;
        }else{
            $this->$right = true;
        }

        $this->_set = true;
        $this->set($right,$this->$right)->save();
        $this->_set = false;
        return $this;
    }
}