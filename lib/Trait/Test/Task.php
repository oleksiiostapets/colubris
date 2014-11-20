<?php
/**
 * Created by PhpStorm.
 * User: alf
 * Date: 11/17/14
 * Time: 2:26 PM
 */

trait Trait_Test_Task {
    private $test_user;
    private $test_project;
    private $test_quote;
    private $test_requirement;
    private $test_task;

    private function atk4_test_cannot_delete_task() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);

        $this->addTestProject();
        $this->addTestQuote();
        $this->addTestRequirement();
        $this->addTestTask();

        try{
            $this->assertThrowException('Exception_API_CannotDelete', $this->test_task, 'prepareForDelete', $args=array($this->test_user));

            $this->test_task->forceDelete();
            $this->test_requirement->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $this->test_task->forceDelete();
            $this->test_requirement->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
            throw $this->exception('User CAN delete task but not allowed');
        }
    }
    private function atk4_test_can_delete_task() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->setRight('can_delete_task',true);
        $r->save();

        $this->addTestProject();
        $this->addTestQuote();
        $this->addTestRequirement();
        $this->addTestTask();

        $task = $this->add('Model_Mock_Task');
        $task->prepareForDelete($this->test_user);
        $task->load($this->test_task['id']);

        try{
            $task->delete();

            $task->load($this->test_task['id']);
            $task->forceDelete();

            $this->test_requirement->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $task->forceDelete();
            $this->test_requirement->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();

            throw $e;
        }
    }
    private function atk4_test_cannot_edit_task() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);

        $this->addTestProject();
        $this->addTestQuote();
        $this->addTestRequirement();
        $this->addTestTask();

        try{
            $this->assertThrowException('Exception_API_CannotEdit', $this->test_task, 'prepareForUpdate', $args=array($this->test_user));

            $this->test_task->forceDelete();
            $this->test_requirement->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $this->test_task->forceDelete();
            $this->test_requirement->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
            throw $this->exception('User CAN edit task but not allowed');
        }
    }
    private function atk4_test_can_edit_task() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->setRight('can_edit_task',true);
        $r->save();

        $this->addTestProject();
        $this->addTestQuote();
        $this->addTestRequirement();
        $this->addTestTask();

        $task = $this->add('Model_Mock_Task');
        $task->prepareForUpdate($this->test_user);
        $task->load($this->test_task['id']);

        try{
            $task->set('name','updated')->save();

            $task->forceDelete();
            $this->test_requirement->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $task->forceDelete();
            $this->test_requirement->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();

            throw $e;
        }
    }
    private function atk4_test_cannot_add_task() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);

        $task = $this->add('Model_Mock_Task');

        try{
            $this->assertThrowException('Exception_API_CannotAdd', $task, 'prepareForInsert', $args=array($this->test_user));

            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $r->delete();
            $this->test_user->forceDelete();
            throw $this->exception('User CAN add task but not allowed');
        }
    }
    private function atk4_test_can_add_task() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->setRight('can_add_task',true);
        $r->save();

        $this->addTestProject();
        $this->addTestQuote();
        $this->addTestRequirement();

        $task = $this->add('Model_Mock_Task');
        $task->prepareForInsert($this->test_user);

        $task->set('name','TestTask'.time());

        try{
            $task->save();

            $task->forceDelete();
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
    private function atk4_test_cannot_see_task() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);

        $task = $this->add('Model_Task');

        try{
            $this->assertThrowException('Exception_API_CannotSee', $task, 'prepareForSelect', $args=array($this->test_user));

            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $r->delete();
            $this->test_user->forceDelete();
            throw $this->exception('User CAN see tasks but not allowed');
        }
    }
    private function atk4_test_can_see_task() {
        $this->addTestUser();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($this->test_user['id']);
        $r->setRight('can_see_tasks',true);
        $r->save();

        $this->addTestProject();
        $this->addTestQuote();
        $this->addTestRequirement();
        $this->addTestTask();


        $task = $this->add('Model_Task');
        $task->prepareForSelect($this->test_user);
        $task->load($this->test_task['id']);

        $data = $task->get();

        try {
            $this->assertTrue(array_key_exists('name',$data), 'User cannot see task field name!');

            $this->test_task->forceDelete();
            $this->test_requirement->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();
        }catch (Exception $e){
            $this->test_task->forceDelete();
            $this->test_requirement->forceDelete();
            $this->test_quote->forceDelete();
            $this->test_project->forceDelete();
            $r->delete();
            $this->test_user->forceDelete();

            throw $e;
        }
    }
    private function addTestTask(){
        $this->test_task = $this->add('Model_Mock_Task');
        $this->test_task->set('requirement_id',$this->test_requirement['id']);
        $this->test_task->set('name','TestTask'.time());
        $this->test_task->set('descr_original','wtyrjgh astdhfg');
        $this->test_task->set('user_id',$this->test_user['id']);
        $this->test_task->save();
    }
}