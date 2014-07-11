<?php
/**
 *Created by Konstantin Kolodnitsky
 * Date: 11.07.14
 * Time: 11:38
 */
class Model_User_Right extends Model_Auditable{
    //task
    public $can_see_tasks;
    public $can_add_task;
    public $can_edit_task;
    public $can_delete_task;
    public $can_add_comment_to_task;
    //Project
    public $can_see_projects;
    public $can_add_projects;
    public $can_edit_projects;
    public $can_delete_projects;
    //Quote
    public $can_add_quote;//Request for quotation
    public $can_edit_quote;
    public $can_delete_quote;
    public $can_submit_for_quotation;
    //Requirement
    public $can_add_requirement;
    public $can_edit_requirement;
    public $can_delete_requirement;
    public $can_add_comment_to_requirement;
    //Setting
    public $can_see_settings;
    public $can_edit_settings;
    //User
    public $can_see_users;
    public $can_manage_users;
    //Rates
    public $can_see_rates;
    public $can_manage_rates;
    //Developers
    public $can_see_developers;
    public $can_manage_developers;
    //Clients
    public $can_see_clients;
    public $can_manage_clients;
    //Reports
    public $can_see_reports;
    //Deleted Items
    public $can_see_deleted;
    public $can_restore_deleted;
    //Logs
    public $can_see_logs;
    //Mics
    public $can_see_dashboard;
    public $can_move_to_from_archive;
    public $can_track_time;
    public $can_login_as_any_user;

    function init(){
        parent::init();
    }
    
    
    //////////////////
    //Right patterns//
    //////////////////
    private function getDeveloperPattern(){
        $rights_pattern = array(
            //task
            'can_see_tasks'=>true,
            'can_add_task'=>true,
            'can_edit_task'=>true,
            'can_delete_task'=>true,
            'can_add_comment_to_task'=>true,
            //Project
            'can_see_projects'=>true,
            'can_add_projects'=>false,
            'can_edit_projects'=>false,
            'can_delete_projects'=>false,
            //Quote
            'can_add_quote'=>false,//Request for quotation
            'can_edit_quote'=>false,
            'can_delete_quote'=>false,
            'can_submit_for_quotation'=>false,
            //Requirement
            'can_add_requirement'=>false,
            'can_edit_requirement'=>false,
            'can_delete_requirement'=>false,
            'can_add_comment_to_requirement'=>true,
            //Setting
            'can_see_settings'=>true,
            'can_edit_settings'=>true,
            //User
            'can_see_users'=>false,
            'can_manage_users'=>false,
            //Rates
            'can_see_rates'=>false,
            'can_manage_rates'=>false,
            //Developers
            'can_see_developers'=>false,
            'can_manage_developers'=>false,
            //Clients
            'can_see_clients'=>false,
            'can_manage_clients'=>false,
            //Reports
            'can_see_reports'=>true,
            //Deleted Items
            'can_see_deleted'=>false,
            'can_restore_deleted'=>false,
            //Logs
            'can_see_logs'=>false,
            //Mics
            'can_see_dashboard'=>true,
            'can_move_to_from_archive'=>true,
            'can_track_time'=>true,
            'can_login_as_any_user'=>false,
        );
        return $rights_pattern;
    }
    private function getClientPattern(){
        $rights_pattern = array(
            //task
            'can_see_tasks'=>true,
            'can_add_task'=>true,
            'can_edit_task'=>false,
            'can_delete_task'=>true,
            'can_add_comment_to_task'=>true,
            //Project
            'can_see_projects'=>true,
            'can_add_projects'=>true,
            'can_edit_projects'=>false,
            'can_delete_projects'=>false,
            //Quote
            'can_add_quote'=>true,//Request for quotation
            'can_edit_quote'=>false,
            'can_delete_quote'=>false,
            'can_submit_for_quotation'=>false,
            //Requirement
            'can_add_requirement'=>true,
            'can_edit_requirement'=>true,
            'can_delete_requirement'=>true,
            'can_add_comment_to_requirement'=>true,
            //Setting
            'can_see_settings'=>true,
            'can_edit_settings'=>true,
            //User
            'can_see_users'=>false,
            'can_manage_users'=>false,
            //Rates
            'can_see_rates'=>false,
            'can_manage_rates'=>false,
            //Developers
            'can_see_developers'=>false,
            'can_manage_developers'=>false,
            //Clients
            'can_see_clients'=>false,
            'can_manage_clients'=>false,
            //Reports
            'can_see_reports'=>false,
            //Deleted Items
            'can_see_deleted'=>false,
            'can_restore_deleted'=>false,
            //Logs
            'can_see_logs'=>false,
            //Mics
            'can_see_dashboard'=>true,
            'can_move_to_from_archive'=>false,
            'can_track_time'=>false,
            'can_login_as_any_user'=>false,
        );
        return $rights_pattern;
    }
    private function getSalesManagerPattern(){
        $rights_pattern = array(
            //task
            'can_see_tasks'=>false,
            'can_add_task'=>false,
            'can_edit_task'=>false,
            'can_delete_task'=>false,
            'can_add_comment_to_task'=>false,
            //Project
            'can_see_projects'=>false,
            'can_add_projects'=>false,
            'can_edit_projects'=>false,
            'can_delete_projects'=>false,
            //Quote
            'can_add_quote'=>true,//Request for quotation
            'can_edit_quote'=>false,
            'can_delete_quote'=>false,
            'can_submit_for_quotation'=>false,
            //Requirement
            'can_add_requirement'=>true,
            'can_edit_requirement'=>false,
            'can_delete_requirement'=>false,
            'can_add_comment_to_requirement'=>false,
            //Setting
            'can_see_settings'=>true,
            'can_edit_settings'=>true,
            //User
            'can_see_users'=>false,
            'can_manage_users'=>false,
            //Rates
            'can_see_rates'=>false,
            'can_manage_rates'=>false,
            //Developers
            'can_see_developers'=>false,
            'can_manage_developers'=>false,
            //Clients
            'can_see_clients'=>false,
            'can_manage_clients'=>false,
            //Reports
            'can_see_reports'=>false,
            //Deleted Items
            'can_see_deleted'=>false,
            'can_restore_deleted'=>false,
            //Logs
            'can_see_logs'=>false,
            //Mics
            'can_see_dashboard'=>true,
            'can_move_to_from_archive'=>true,
            'can_track_time'=>false,
            'can_login_as_any_user'=>false,
        );
        return $rights_pattern;
    }
    private function getManagerPattern(){
        $rights_pattern = array(
            //task
            'can_see_tasks'=>true,
            'can_add_task'=>true,
            'can_edit_task'=>true,
            'can_delete_task'=>true,
            'can_add_comment_to_task'=>true,
            //Project
            'can_see_projects'=>true,
            'can_add_projects'=>true,
            'can_edit_projects'=>true,
            'can_delete_projects'=>true,
            //Quote
            'can_add_quote'=>true,//Request for quotation
            'can_edit_quote'=>true,
            'can_delete_quote'=>true,
            'can_submit_for_quotation'=>true,
            //Requirement
            'can_add_requirement'=>true,
            'can_edit_requirement'=>true,
            'can_delete_requirement'=>true,
            'can_add_comment_to_requirement'=>true,
            //Setting
            'can_see_settings'=>true,
            'can_edit_settings'=>true,
            //User
            'can_see_users'=>false,
            'can_manage_users'=>false,
            //Rates
            'can_see_rates'=>true,
            'can_manage_rates'=>true,
            //Developers
            'can_see_developers'=>false,
            'can_manage_developers'=>false,
            //Clients
            'can_see_clients'=>true,
            'can_manage_clients'=>true,
            //Reports
            'can_see_reports'=>true,
            //Deleted Items
            'can_see_deleted'=>true,
            'can_restore_deleted'=>true,
            //Logs
            'can_see_logs'=>false,
            //Mics
            'can_see_dashboard'=>true,
            'can_move_to_from_archive'=>true,
            'can_track_time'=>true,
            'can_login_as_any_user'=>false,
        );
        return $rights_pattern;
    }
    private function getadminPattern(){
        $rights_pattern = array(
            //task
            'can_see_tasks'=>false,
            'can_add_task'=>false,
            'can_edit_task'=>false,
            'can_delete_task'=>false,
            'can_add_comment_to_task'=>false,
            //Project
            'can_see_projects'=>false,
            'can_add_projects'=>false,
            'can_edit_projects'=>false,
            'can_delete_projects'=>false,
            //Quote
            'can_add_quote'=>false,//Request for quotation
            'can_edit_quote'=>false,
            'can_delete_quote'=>false,
            'can_submit_for_quotation'=>false,
            //Requirement
            'can_add_requirement'=>true,
            'can_edit_requirement'=>true,
            'can_delete_requirement'=>true,
            'can_add_comment_to_requirement'=>true,
            //Setting
            'can_see_settings'=>true,
            'can_edit_settings'=>true,
            //User
            'can_see_users'=>true,
            'can_manage_users'=>true,
            //Rates
            'can_see_rates'=>true,
            'can_manage_rates'=>true,
            //Developers
            'can_see_developers'=>true,
            'can_manage_developers'=>true,
            //Clients
            'can_see_clients'=>false,
            'can_manage_clients'=>false,
            //Reports
            'can_see_reports'=>false,
            //Deleted Items
            'can_see_deleted'=>false,
            'can_restore_deleted'=>false,
            //Logs
            'can_see_logs'=>true,
            //Mics
            'can_see_dashboard'=>true,
            'can_move_to_from_archive'=>false,
            'can_track_time'=>false,
            'can_login_as_any_user'=>true,
        );
        return $rights_pattern;
    }
    /*
     * New user's rights
     */
    public function save_new_user_as_developer(){
        if($this->loaded()){
            throw $this->exception('This user exists and already has his rights saved. Use make_as_developer() to change them');
        }else{
            $rights = $this->getDeveloperPattern();
            $this->set($rights)->save();
            return $this;
        }
    }
    public function save_new_user_as_client(){
        if($this->loaded()){
            throw $this->exception('This user exists and already has his rights saved. Use make_as_client() to change them');
        }else{
            $rights = $this->getClientPattern();
            $this->set($rights)->save();
            return $this;
        }
    }
    public function save_new_user_as_salesManager(){
        if($this->loaded()){
            throw $this->exception('This user exists and already has his rights saved. Use make_as_salesManager() to change them');
        }else{
            $rights = $this->getSalesManagerPattern();
            $this->set($rights)->save();
            return $this;
        }
    }
    public function save_new_user_as_manager(){
        if($this->loaded()){
            throw $this->exception('This user exists and already has his rights saved. Use make_as_manager() to change them');
        }else{
            $rights = $this->getManagerPattern();
            $this->set($rights)->save();
            return $this;
        }
    }
    public function save_new_user_as_admin(){
        if($this->loaded()){
            throw $this->exception('This user exists and already has his rights saved. Use make_as_admin() to change them');
        }else{
            $rights = $this->getadminPattern();
            $this->set($rights)->save();
            return $this;
        }
    }
    /*
     * Set rights for existing user
     */
    public function make_existing_user_as_developer(){
        if($this->loaded()){
            $rights = $this->getDeveloperPattern();
            $this->set($rights)->save();
            return $this;
        }else{
            throw $this->exception('The user is not loaded');
        }
    }
    public function make_existing_user_as_client(){
        if($this->loaded()){
            $rights = $this->getClientPattern();
            $this->set($rights)->save();
            return $this;
        }else{
            throw $this->exception('The user is not loaded');
        }
    }
    public function make_existing_user_as_salesManager(){
        if($this->loaded()){
            $rights = $this->getSalesManagerPattern();
            $this->set($rights)->save();
            return $this;
        }else{
            throw $this->exception('The user is not loaded');
        }
    }
    public function make_existing_user_as_manager(){
        if($this->loaded()){
            $rights = $this->getManagerPattern();
            $this->set($rights)->save();
            return $this;
        }else{
            throw $this->exception('The user is not loaded');
        }
    }
    public function make_existing_user_as_admin(){
        if($this->loaded()){
            $rights = $this->getadminPattern();
            $this->set($rights)->save();
            return $this;
        }else{
            throw $this->exception('The user is not loaded');
        }
    }
    
    
    
    //////////////
    //Get rights//
    //////////////
    private function getRight($name){
        /*$user = $this->app->auth->model;//TODO Use app->auth->model or $this?
        if($user){
            return $user[$name];
        }else{
            throw $this->exception('User is not logged in');
        }*/
        return true; //TODO Temporary!!!!!!!!!!!!!!!!!!
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