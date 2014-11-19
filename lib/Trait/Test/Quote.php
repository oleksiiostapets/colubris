<?php
/**
 * Created by PhpStorm.
 * User: alf
 * Date: 11/17/14
 * Time: 2:26 PM
 */

trait Trait_Test_Quote {
    private $test_user;
    private $test_project;
    private $test_quote;

    /**
     * QUOTES PERMISSIONS
     */
    private function atk4_test_cannot_delete_quote() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);

        $this->addTestProject();
        $this->addTestQuote();

        try{
            $this->assertThrowException('Exception_API_CannotDelete', $this->test_quote, 'prepareForDelete', $args=array($this->test_user));

            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
            throw $this->exception('User CAN delete quote but not allowed');
        }
    }
    private function atk4_test_can_delete_quote() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->setRight('can_delete_quote',true);
        $r->save();

        $this->addTestProject();
        $this->addTestQuote();

        $q2 = $this->add('Model_Quote');
        $q2->prepareForDelete($this->test_user);
        $q2->load($this->test_quote['id']);

        try{
            $q2->delete();

            $q2->load($this->test_quote['id']);
            $q2->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $q2->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();

            throw $e;
        }
    }
    private function atk4_test_cannot_edit_quote() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->save();

        $this->addTestProject();
        $this->addTestQuote();

        try{
            $this->assertThrowException('Exception_API_CannotEdit', $this->test_quote, 'prepareForUpdate', $args=array($this->test_user));

            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
            throw $this->exception('User CAN edit quote but not allowed');
        }
    }
    private function atk4_test_can_edit_quote() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->setRight('can_edit_quote',true);
        $r->save();

        $this->addTestProject();
        $this->addTestQuote();

        $q2 = $this->add('Model_Quote');
        $q2->prepareForUpdate($this->test_user);
        $q2->load($this->test_quote['id']);

        try{
            $q2->set('name','updated')->save();

            $q2->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $q2->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();

            throw $e;
        }
    }
    private function atk4_test_cannot_add_quote() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->save();

        $q2 = $this->add('Model_Quote');

        try{
            $this->assertThrowException('Exception_API_CannotAdd', $q2, 'prepareForInsert', $args=array($this->test_user));

            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $r->delete();
            $this->test_user->forceDelete();
            throw $this->exception('User CAN add quote but not allowed');
        }

    }
    private function atk4_test_can_add_quote() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->setRight('can_add_quote',true);
        $r->save();

        $this->addTestProject();

        $q2 = $this->add('Model_Quote');
        $q2->prepareForInsert($this->test_user);

        $q2->set('project_id',$this->test_project['id']);
        $q2->set('name','TestQuote'.time());
        $q2->set('amount','50');
        $q2->set('rate','40.0');
        $q2->set('currency','EUR');

        try{
            $q2->save();

            $q2->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $q2->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();

            throw $e;
        }
    }
    private function atk4_test_cannot_see_quote() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->save();

        $q2 = $this->add('Model_Quote');

        try{
            $this->assertThrowException('Exception_API_CannotSee', $q2, 'prepareForSelect', $args=array($this->test_user));

            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $r->delete();
            $this->test_user->forceDelete();
            throw $this->exception('User CAN add quote but not allowed');
        }
    }
    private function atk4_test_can_see_quote() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->setRight('can_see_quotes',true);
        $r->save();

        $this->addTestProject();
        $this->addTestQuote();

        $q2 = $this->add('Model_Quote');
        $q2->prepareForSelect($this->test_user);
        $q2->load($this->test_quote['id']);

        $data = $q2->get();

        try {
            $this->assertTrue(array_key_exists('name',$data), 'User cannot see quote field name!');
            $this->assertTrue(array_key_exists('user_id',$data), 'User cannot see quote field user_id!');
            $this->assertTrue(array_key_exists('general_description',$data), 'User cannot see quote field general_description!');
            $this->assertTrue(array_key_exists('issued',$data), 'User cannot see quote field issued!');
            $this->assertTrue(array_key_exists('duration',$data), 'User cannot see quote field duration!');
            $this->assertTrue(array_key_exists('deadline',$data), 'User cannot see quote field deadline!');
            $this->assertTrue(array_key_exists('durdead',$data), 'User cannot see quote field durdead!');
            $this->assertTrue(array_key_exists('html',$data), 'User cannot see quote field html!');
            $this->assertTrue(array_key_exists('status',$data), 'User cannot see quote field status!');
            $this->assertTrue(array_key_exists('is_deleted',$data), 'User cannot see quote field is_deleted!');
            $this->assertTrue(array_key_exists('deleted_id',$data), 'User cannot see quote field deleted_id!');
            $this->assertTrue(array_key_exists('organisation_id',$data), 'User cannot see quote field organisation_id!');
            $this->assertTrue(array_key_exists('created_dts',$data), 'User cannot see quote field created_dts!');
            $this->assertTrue(array_key_exists('updated_dts',$data), 'User cannot see quote field updated_dts!');
            $this->assertTrue(array_key_exists('expires_dts',$data), 'User cannot see quote field expires_dts!');
            $this->assertTrue(array_key_exists('is_archived',$data), 'User cannot see quote field is_archived!');
            $this->assertTrue(array_key_exists('warranty_end',$data), 'User cannot see quote field warranty_end!');
            $this->assertTrue(array_key_exists('show_time_to_client',$data), 'User cannot see quote field show_time_to_client!');
            $this->assertTrue(array_key_exists('client_id',$data), 'User cannot see quote field client_id!');
            $this->assertTrue(array_key_exists('client_name',$data), 'User cannot see quote field client_name!');
            $this->assertTrue(array_key_exists('client_email',$data), 'User cannot see quote field client_email!');
            $this->assertTrue(array_key_exists('estimated',$data), 'User cannot see quote field estimated!');
            $this->assertTrue(array_key_exists('spent_time',$data), 'User cannot see quote field spent_time!');

            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();

        }catch(Exception $e){
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();

            throw $e;
        }
    }
    private function addTestUser(){
        $this->test_user = $this->add('Model_Mock_User');
        $this->test_user->set('name','TestUser_'.time());
        $this->test_user->save();
    }
    private function addTestProject(){
        $this->test_project = $this->add('Model_Project');
        $this->test_project->set('name','TestProject'.time());
        $this->test_project->save();
    }
    private function addTestQuote(){
        $this->test_quote = $this->add('Model_Quote');
        $this->test_quote->set('project_id',$this->test_project['id']);
        $this->test_quote->set('name','TestQuote'.time());
        $this->test_quote->set('amount','50');
        $this->test_quote->set('rate','40.0');
        $this->test_quote->set('currency','EUR');
        $this->test_quote->save();
    }
}