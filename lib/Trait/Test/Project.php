<?php
/**
 * Created by PhpStorm.
 * User: alf
 * Date: 11/17/14
 * Time: 2:26 PM
 */

trait Trait_Test_Project {
    private $test_user;
    private $test_project;

    private function atk4_test_cannot_delete_project() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);

        $this->addTestProject();

        try{
            $this->assertThrowException('Exception_API_CannotDelete', $this->test_project, 'prepareForDelete', $args=array($this->test_user));

            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
            throw $this->exception('User CAN delete project but not allowed');
        }
    }
    private function atk4_test_can_delete_project() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->setRight('can_delete_projects',true);
        $r->save();

        $this->addTestProject();

        $q2 = $this->add('Model_Project');
        $q2->prepareForDelete($this->test_user);
        $q2->load($this->test_project['id']);

        try{
            $q2->delete();

            $q2->load($this->test_project['id']);
            $q2->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $q2->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();

            throw $e;
        }
    }
    private function atk4_test_cannot_edit_project() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);

        $this->addTestProject();

        try{
            $this->assertThrowException('Exception_API_CannotEdit', $this->test_project, 'prepareForUpdate', $args=array($this->test_user));

            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
            throw $this->exception('User CAN edit project but not allowed');
        }
    }
    private function atk4_test_can_edit_project() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->setRight('can_edit_projects',true);
        $r->save();

        $this->addTestProject();

        $q2 = $this->add('Model_Project');
        $q2->prepareForUpdate($this->test_user);
        $q2->load($this->test_project['id']);

        try{
            $q2->set('name','updated')->save();

            $q2->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $q2->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();

            throw $e;
        }
    }
    private function atk4_test_cannot_add_project() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);

        $p = $this->add('Model_Project');

        try{
            $this->assertThrowException('Exception_API_CannotAdd', $p, 'prepareForInsert', $args=array($this->test_user));

            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $r->delete();
            $this->test_user->forceDelete();
            throw $this->exception('User CAN add project but not allowed');
        }
    }
    private function atk4_test_can_add_project() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->setRight('can_add_projects',true);
        $r->save();

        $p = $this->add('Model_Project');
        $p->prepareForInsert($this->test_user);

        $p->set('name','TestProject'.time());
        $p->set('descr','qwe');
        $p->set('demo_url','http://test.com');

        try{
            $p->save();

            $p->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $p->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();

            throw $e;
        }
    }
    private function atk4_test_cannot_see_project() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);

        $p = $this->add('Model_Project');

        try{
            $this->assertThrowException('Exception_API_CannotSee', $p, 'prepareForSelect', $args=array($this->test_user));

            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $r->delete();
            $this->test_user->forceDelete();
            throw $this->exception('User CAN add project but not allowed');
        }
    }
    private function atk4_test_can_see_project() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->setRight('can_see_projects',true);
        $r->save();

        $this->addTestProject();

        $p = $this->add('Model_Project');
        $p->prepareForSelect($this->test_user);
        $p->load($this->test_project['id']);

        $data = $p->get();

        try {
            $this->assertTrue(array_key_exists('name',$data), 'User cannot see project field name!');
            $this->assertTrue(array_key_exists('descr',$data), 'User cannot see project field descr!');
            $this->assertTrue(array_key_exists('client_id',$data), 'User cannot see project field client_id!');
            $this->assertTrue(array_key_exists('demo_url',$data), 'User cannot see project field demo_url!');
            $this->assertTrue(array_key_exists('prod_url',$data), 'User cannot see project field prod_url!');
            $this->assertTrue(array_key_exists('repository',$data), 'User cannot see project field repository!');
            $this->assertTrue(array_key_exists('organisation_id',$data), 'User cannot see project field organisation_id!');
            $this->assertTrue(array_key_exists('is_deleted',$data), 'User cannot see project field is_deleted!');
            $this->assertTrue(array_key_exists('is_deleted',$data), 'User cannot see project field is_deleted!');
            $this->assertTrue(array_key_exists('deleted_id',$data), 'User cannot see project field deleted_id!');
            $this->assertTrue(array_key_exists('spent_time',$data), 'User cannot see project field spent_time!');

            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();

            throw $e;
        }
    }
}