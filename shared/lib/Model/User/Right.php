<?php
/**
 *Created by Konstantin Kolodnitsky
 * Date: 11.07.14
 * Time: 11:38
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
    public function save_new_user_as_developer(){
        $this->setPatternForNew($this->developer_pattern);

    }
    public function save_new_user_as_client(){
        $this->setPatternForNew($this->client_pattern);
    }
    public function save_new_user_as_salesManager(){
        $this->setPatternForNew($this->sales_manager_pattern);
    }
    public function save_new_user_as_manager(){
        $this->setPatternForNew($this->manager_pattern);
    }
    public function save_new_user_as_admin(){
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
    public function make_existing_user_as_developer(){
        $this->setPatternForExisting($this->developer_pattern);
    }
    public function make_existing_user_as_client(){
        $this->setPatternForExisting($this->client_pattern);
    }
    public function make_existing_user_as_salesManager(){
        $this->setPatternForExisting($this->sales_manager_pattern);
    }
    public function make_existing_user_as_manager(){
        $this->setPatternForExisting($this->manager_pattern);
    }
    public function make_existing_user_as_admin(){
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
    private function getRight($right_name){
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
    public function can_see_tasks(){return $this->getRight('can_see_tasks');}
    public function can_add_task(){return $this->getRight('can_add_task');}
    public function can_edit_task(){return $this->getRight('can_edit_task');}
    public function can_delete_task(){return $this->getRight('can_delete_task');}
    public function can_add_comment_to_task(){return $this->getRight('can_add_comment_to_task');}
    //Project
    public function can_see_projects(){return $this->getRight('can_see_projects');}
    public function can_add_projects(){return $this->getRight('can_add_projects');}
    public function can_edit_projects(){return $this->getRight('can_edit_projects');}
    public function can_delete_projects(){return $this->getRight('can_delete_projects');}
    //Quote
    public function can_add_quote(){return $this->getRight('can_add_quote');}//Request for quotation
    public function can_edit_quote(){return $this->getRight('can_edit_quote');}
    public function can_delete_quote(){return $this->getRight('can_delete_quote');}
    public function can_submit_for_quotation(){return $this->getRight('can_submit_for_quotation');}
    //Requirement
    public function can_add_requirement(){return $this->getRight('can_add_requirement');}
    public function can_edit_requirement(){return $this->getRight('can_edit_requirement');}
    public function can_delete_requirement(){return $this->getRight('can_delete_requirement');}
    public function can_add_comment_to_requirement(){return $this->getRight('can_add_comment_to_requirement');}
    //Setting
    public function can_see_settings(){return $this->getRight('can_see_settings');}
    public function can_edit_settings(){return $this->getRight('can_edit_settings');}
    //User
    public function can_see_users(){return $this->getRight('can_see_users');}
    public function can_manage_users(){return $this->getRight('can_manage_users');}
    //Rates
    public function can_see_rates(){return $this->getRight('can_see_rates');}
    public function can_manage_rates(){return $this->getRight('can_manage_rates');}
    //Developers
    public function can_see_developers(){return $this->getRight('can_see_developers');}
    public function can_manage_developers(){return $this->getRight('can_manage_developers');}
    //Clients
    public function can_see_clients(){return $this->getRight('can_see_clients');}
    public function can_manage_clients(){return $this->getRight('can_manage_clients');}
    //Reports
    public function can_see_reports(){return $this->getRight('can_see_reports');}
    //Deleted Items
    public function can_see_deleted(){return $this->getRight('can_see_deleted');}
    public function can_restore_deleted(){return $this->getRight('can_restore_deleted');}
    //Logs
    public function can_see_logs(){return $this->getRight('can_see_logs');}
    //Mics
    public function can_see_dashboard(){return $this->getRight('can_see_dashboard');}
    public function can_move_to_from_archive(){return $this->getRight('can_move_to_from_archive');}
    public function can_track_time(){return $this->getRight('can_track_time');}
    public function can_login_as_any_user(){return $this->getRight('can_login_as_any_user');}

    /////////////////
    //Toggle rights//
    /////////////////
    private function toggle_right($right){
        if($this->$right){
            $this->$right = false;
        }else{
            $this->$right = true;
        }
        $this->set($right,$this->$right)->save();
        return $this;
    }
    //task
    public function toggle_can_see_tasks(){
        $this->toggle_right('can_see_tasks');
        return $this;
    }
    public function toggle_can_add_task(){
        $this->toggle_right('can_add_task');
        return $this;
    }
    public function toggle_can_edit_task(){
        $this->toggle_right('can_edit_task');
        return $this;
    }
    public function toggle_can_delete_task(){
        $this->toggle_right('can_delete_task');
        return $this;
    }
    public function toggle_can_add_comment_to_task(){
        $this->toggle_right('can_add_comment_to_task');
        return $this;
    }
    //Project
    public function toggle_can_see_projects(){
        $this->toggle_right('can_see_projects');
        return $this;
    }
    public function toggle_can_add_projects(){
        $this->toggle_right('can_add_projects');
        return $this;
    }
    public function toggle_can_edit_projects(){
        $this->toggle_right('can_edit_projects');
        return $this;
    }
    public function toggle_can_delete_projects(){
        $this->toggle_right('can_delete_projects');
        return $this;
    }
    //Quote
    public function toggle_can_add_quote(){
        $this->toggle_right('can_add_quote');
        return $this;
    }//Request for quotation
    public function toggle_can_edit_quote(){
        $this->toggle_right('can_edit_quote');
        return $this;
    }
    public function toggle_can_delete_quote(){
        $this->toggle_right('can_delete_quote');
        return $this;
    }
    public function toggle_can_submit_for_quotation(){
        $this->toggle_right('can_submit_for_quotation');
        return $this;
    }
    //Requirement
    public function toggle_can_add_requirement(){
        $this->toggle_right('can_add_requirement');
        return $this;
    }
    public function toggle_can_edit_requirement(){}
    public function toggle_can_delete_requirement(){}
    public function toggle_can_add_comment_to_requirement(){}
    //Setting
    public function toggle_can_see_settings(){}
    public function toggle_can_edit_settings(){}
    //User
    public function toggle_can_see_users(){}
    public function toggle_can_manage_users(){}
    //Rates
    public function toggle_can_see_rates(){}
    public function toggle_can_manage_rates(){}
    //Developers
    public function toggle_can_see_developers(){}
    public function toggle_can_manage_developers(){}
    //Clients
    public function toggle_can_see_clients(){}
    public function toggle_can_manage_clients(){}
    //Reports
    public function toggle_can_see_reports(){}
    //Deleted Items
    public function toggle_can_see_deleted(){}
    public function toggle_can_restore_deleted(){}
    //Logs
    public function toggle_can_see_logs(){}
    //Mics
    public function toggle_can_see_dashboard(){}
    public function toggle_can_move_to_from_archive(){}
    public function toggle_can_track_time(){}
    public function toggle_can_login_as_any_user(){}
}