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
 *     - getRight($right_name).
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
class Model_User_Right extends Model_Auditable{
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
        //Quote
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
    );
    private $developer_pattern = 'can_see_tasks,can_add_task,can_edit_task,can_delete_task,can_add_comment_to_task,can_see_projects,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_reports,can_see_dashboard,can_move_to_from_archive,can_track_time';
    private $client_pattern = 'can_see_tasks,can_add_task,can_delete_task,can_add_comment_to_task,can_see_projects,can_add_projects,can_add_quote,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_dashboard';
    private $sales_manager_pattern = 'can_add_quote,can_add_requirement,can_see_settings,can_edit_settings,can_see_dashboard,can_move_to_from_archive';
    private $manager_pattern = 'can_see_tasks,can_add_task,can_edit_task,can_delete_task,can_add_comment_to_task,can_see_projects,can_add_projects,can_edit_projects,can_delete_projects,can_add_quote,can_edit_quote,can_delete_quote,can_submit_for_quotation,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_rates,can_manage_rates,can_see_clients,can_manage_clients,can_see_reports,can_see_deleted,can_restore_deleted,can_see_dashboard,can_move_to_from_archive,can_track_time';
    private $admin_pattern = 'can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_users,can_manage_users,can_see_rates,can_manage_rates,can_see_developers,can_manage_developers,can_see_logs,can_see_dashboard,can_login_as_any_user';

    function init(){
        parent::init();
//        $this->addField('user_id');
        $this->hasOne('User');
        $this->addField('right')->type('text');
        $this->user = $this->app->auth->model;
    }

    private $_set = false;
    function set($name,$value=UNDEFINED) {
        if ($this->_set) {
            parent::set($name,$value);
            return $this;
        }
        throw $this->exception('This method is private in this model');
    }

    function setRight($what,$can=true) {
        if ($this->checkRight($what)) {
            $curr = $this->get('right');
            $arr = explode(',',$curr);
            $new_curr = $arr;
            if ($can) {
                $already_exist = false;
                foreach ($arr as $k=>$v) {
                    if ($v == $what) {
                        $already_exist = true;
                    }
                }
                if (!$already_exist) {
                    $arr[] = $what;
                }
                $new_curr = implode(',',$arr);
            } else {
                $new_arr = array();
                foreach ($arr as $k=>$v) {
                    if ($v != $what) {
                        $new_arr[] = $v;
                    }
                }
                $new_curr = implode(',',$new_arr);
            }
            $this->_set = true;
            $this->set('right',$new_curr)->saveLater();
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
    private function setPatternForNew($pattern){
        $this->tryLoadBy('user_id',$this->user->id);
        if($this->loaded()){
            throw $this->exception('This user exists and already has his rights saved. Use make_as_() to change them');
        }else{
            $this->_set = true;
            $this->set('right',$pattern)->save();
            $this->_set = false;
            return $this;
        }
    }
    public function saveNewUserAsDeveloper(){
        $this->setPatternForNew($this->developer_pattern);

    }
    public function saveNewUserAsClient(){
        $this->setPatternForNew($this->client_pattern);
    }
    public function saveNewUserAsSalesManager(){
        $this->setPatternForNew($this->sales_manager_pattern);
    }
    public function saveNewUserAsManager(){
        $this->setPatternForNew($this->manager_pattern);
    }
    public function saveNewUserAsAdmin(){
        $this->setPatternForNew($this->admin_pattern);
    }
    /*
     * Set rights for existing user
     */
    private function setPatternForExisting($pattern){
        $this->tryLoadBy('user_id',$this->user->id);
        if($this->loaded()){
            $this->_set = true;
            $this->set('right',$pattern)->save();
            $this->_set = false;
            return $this;
        }else{
            throw $this->exception('There is no rights record for current user. Use "save_new_user_as_()" method to set user rights.');
        }
    }
    public function makeExistingUserAsDeveloper(){
        $this->setPatternForExisting($this->developer_pattern);
    }
    public function makeExistingUserAsClient(){
        $this->setPatternForExisting($this->client_pattern);
    }
    public function makeExistingUserAsSalesManager(){
        $this->setPatternForExisting($this->sales_manager_pattern);
    }
    public function makeExistingUserAsManager(){
        $this->setPatternForExisting($this->manager_pattern);
    }
    public function makeExistingUserAsAadmin(){
        $this->setPatternForExisting($this->admin_pattern);
    }
    
    
    
    //////////////
    //Get rights//
    //////////////
    private function fetchRights($rights_string,$right_name){
        $rights_array = explode(',',$rights_string);
        if(in_array($right_name,$rights_array,true)){
            return true;
        }
        return false;
    }
    public function getRight($right_name){
        if(!$this->checkRight($right_name)) throw $this->exception('There is no such an access right defined');
        if($this->user->loaded()){
            $this->tryLoadBy('user_id',$this->user['id']);
            if($this->loaded()){
                $rights_array = $this->getRows();
                if(!$rights_array[0]['right']) throw $this->exception('User rights setup as NULL. User ID is "'.$this->user['id'].'"');
                $right = $this->fetchRights($rights_array[0]['right'],$right_name);
                return $right;
            }else{
                throw $this->exception('This user has no rights being setup. User ID is "'.$this->user['id'].'"');
            }
        }else{
            throw $this->exception('User is not logged in');
        }
    }
    //task
    public function canSeeTasks(){return $this->getRight('can_see_tasks');}
    public function canAddTask(){return $this->getRight('can_add_task');}
    public function canEditTask(){return $this->getRight('can_edit_task');}
    public function canDeleteTask(){return $this->getRight('can_delete_task');}
    public function canAddCommentToTask(){return $this->getRight('can_add_comment_to_task');}
    //Project
    public function canSeeProjects(){return $this->getRight('can_see_projects');}
    public function canAddProjects(){return $this->getRight('can_add_projects');}
    public function canEditProjects(){return $this->getRight('can_edit_projects');}
    public function canDeleteProjects(){return $this->getRight('can_delete_projects');}
    //Quote
    public function canAddQuote(){return $this->getRight('can_add_quote');}//Request for quotation
    public function canEditQuote(){return $this->getRight('can_edit_quote');}
    public function canDeleteQuote(){return $this->getRight('can_delete_quote');}
    public function canSubmitForQuotation(){return $this->getRight('can_submit_for_quotation');}
    //Requirement
    public function canAddRequirement(){return $this->getRight('can_add_requirement');}
    public function canEditRequirement(){return $this->getRight('can_edit_requirement');}
    public function canDeleteRequirement(){return $this->getRight('can_delete_requirement');}
    public function canAddCommentToRequirement(){return $this->getRight('can_add_comment_to_requirement');}
    //Setting
    public function canSeeSettings(){return $this->getRight('can_see_settings');}
    public function canEditSettings(){return $this->getRight('can_edit_settings');}
    //User
    public function canSeeUsers(){return $this->getRight('can_see_users');}
    public function canManageUsers(){return $this->getRight('can_manage_users');}
    //Rates
    public function canSeeRates(){return $this->getRight('can_see_rates');}
    public function canManageRates(){return $this->getRight('can_manage_rates');}
    //Developers
    public function canSeeDevelopers(){return $this->getRight('can_see_developers');}
    public function canManageDevelopers(){return $this->getRight('can_manage_developers');}
    //Clients
    public function canSeeClients(){return $this->getRight('can_see_clients');}
    public function canManageClients(){return $this->getRight('can_manage_clients');}
    //Reports
    public function canSeeReports(){return $this->getRight('can_see_reports');}
    //Deleted Items
    public function canSeeDeleted(){return $this->getRight('can_see_deleted');}
    public function canRestoreDeleted(){return $this->getRight('can_restore_deleted');}
    //Logs
    public function canSeeLogs(){return $this->getRight('can_see_logs');}
    //Mics
    public function canSeeDashboard(){return $this->getRight('can_see_dashboard');}
    public function canMoveToFromArchive(){return $this->getRight('can_move_to_from_archive');}
    public function canTrackTime(){return $this->getRight('can_track_time');}
    public function canLoginAsAnyUser(){return $this->getRight('can_login_as_any_user');}

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
        $this->set($right,$this->$right)->save();
        return $this;
    }
}