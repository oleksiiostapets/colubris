<?php
/**
 * Created by PhpStorm.
 * User: alf
 * Date: 11/17/14
 * Time: 2:26 PM
 */

trait Trait_Test_Requirement {
    private $test_user;
    private $test_project;
    private $test_quote;
    private $test_requirement;

    private function atk4_test_cannot_delete_requirement() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);

        $this->addTestProject();
        $this->addTestQuote();
        $this->addTestRequirement();

        try{
            $this->assertThrowException('Exception_API_CannotDelete', $this->test_requirement, 'prepareForDelete', $args=array($this->test_user));

            $this->test_requirement->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $this->test_requirement->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
            throw $this->exception('User CAN delete requirement but not allowed');
        }
    }
    private function atk4_test_can_delete_requirement() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->setRight('can_delete_requirement',true);
        $r->save();

        $this->addTestProject();
        $this->addTestQuote();
        $this->addTestRequirement();

        $reqv = $this->add('Model_Requirement');
        $reqv->prepareForDelete($this->test_user);
        $reqv->load($this->test_requirement['id']);

        try{
            $reqv->delete();

            $reqv->load($this->test_requirement['id']);
            $reqv->forceDelete();

            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $reqv->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();

            throw $e;
        }
    }
    private function atk4_test_cannot_edit_requirement() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);

        $this->addTestProject();
        $this->addTestQuote();
        $this->addTestRequirement();

        try{
            $this->assertThrowException('Exception_API_CannotEdit', $this->test_requirement, 'prepareForUpdate', $args=array($this->test_user));

            $this->test_requirement->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $this->test_requirement->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
            throw $this->exception('User CAN edit requirement but not allowed');
        }
    }
    private function atk4_test_can_edit_requirement() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->setRight('can_edit_requirement',true);
        $r->save();

        $this->addTestProject();
        $this->addTestQuote();
        $this->addTestRequirement();

        $reqv = $this->add('Model_Requirement');
        $reqv->prepareForUpdate($this->test_user);
        $reqv->load($this->test_requirement['id']);

        try{
            $reqv->set('name','updated')->save();

            $reqv->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $reqv->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();

            throw $e;
        }
    }
    private function atk4_test_cannot_add_requirement() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);

        $reqv = $this->add('Model_Requirement');

        try{
            $this->assertThrowException('Exception_API_CannotAdd', $reqv, 'prepareForInsert', $args=array($this->test_user));

            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $r->delete();
            $this->test_user->forceDelete();
            throw $this->exception('User CAN add requirement but not allowed');
        }
    }
    private function atk4_test_can_add_requirement() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->setRight('can_add_requirement',true);
        $r->save();

        $this->addTestProject();
        $this->addTestQuote();

        $reqv = $this->add('Model_Requirement');
        $reqv->prepareForInsert($this->test_user);

        $reqv->set('name','TestReqv'.time());
        $reqv->set('descr','qwe');

        try{
            $reqv->save();

            $reqv->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $reqv->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();

            throw $e;
        }
    }
    private function atk4_test_cannot_see_requirement() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);

        $reqv = $this->add('Model_Requirement');

        try{
            $this->assertThrowException('Exception_API_CannotSee', $reqv, 'prepareForSelect', $args=array($this->test_user));

            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $r->delete();
            $this->test_user->forceDelete();
            throw $this->exception('User CAN see requirement but not allowed');
        }
    }
    private function atk4_test_can_see_requirement() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->setRight('can_see_quotes',true);
        $r->save();

        $this->addTestProject();
        $this->addTestQuote();
        $this->addTestRequirement();


        $reqv = $this->add('Model_Requirement');
        $reqv->prepareForSelect($this->test_user);
        $reqv->load($this->test_requirement['id']);

        $data = $reqv->get();

        try {
            $this->assertTrue(array_key_exists('name',$data), 'User cannot see requirement field name!');
            $this->assertTrue(array_key_exists('quote_id',$data), 'User cannot see requirement field quote_id!');
            $this->assertTrue(array_key_exists('user_id',$data), 'User cannot see requirement field user_id!');
            $this->assertTrue(array_key_exists('descr',$data), 'User cannot see requirement field descr!');
            $this->assertTrue(array_key_exists('estimate',$data), 'User cannot see requirement field estimate!');
            $this->assertTrue(array_key_exists('is_included',$data), 'User cannot see requirement field is_included!');
            $this->assertTrue(array_key_exists('is_deleted',$data), 'User cannot see requirement field is_deleted!');
            $this->assertTrue(array_key_exists('deleted_id',$data), 'User cannot see requirement field deleted_id!');
            $this->assertTrue(array_key_exists('project_id',$data), 'User cannot see requirement field project_id!');
            $this->assertTrue(array_key_exists('project_name',$data), 'User cannot see requirement field project_name!');
            $this->assertTrue(array_key_exists('spent_time',$data), 'User cannot see requirement field spent_time!');
            $this->assertTrue(array_key_exists('count_comments',$data), 'User cannot see requirement field count_comments!');

            $this->test_requirement->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $this->test_requirement->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();

            throw $e;
        }
    }
    private function addTestRequirement(){
        $this->test_requirement = $this->add('Model_Requirement');
        $this->test_requirement->set('quote_id',$this->test_quote['id']);
        $this->test_requirement->set('name','TestReqv'.time());
        $this->test_requirement->set('descr','wtyrjgh astdhfg');
        $this->test_requirement->set('user_id',$this->test_user['id']);
        $this->test_requirement->save();
    }
}