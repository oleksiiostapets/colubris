<?php
/**
 * Created by PhpStorm.
 * User: alf
 * Date: 11/17/14
 * Time: 2:26 PM
 */

trait Trait_Test_UserRights {

    /**
     * QUOTES PERMISSIONS
     */
    private function atk4_test_can_see_money_true() {
        /**
         * 1. Добавляем юзера
         * 2. Устанавливаем ему право видеть финансы
         * 3. Получаем проекты этого пользователя (прокты, которые может видеть этот пользователь)
         * 4. Проверяем, вернулась ли нам финансовая информация (например, rate)
         * 5. Удаляем юзера и его права
         */
        $u = $this->add('Model_Mock_User');
        $u->set('name','TestUser_'.time());
        $u->save();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($u['id']);
        $r->setRight('can_see_quotes',true);
        $r->setRight('can_see_finance',true);
        $r->save();

        $p = $this->add('Model_Project');
        $p->set('name','TestProject'.time());
        $p->save();

        $q = $this->add('Model_Quote');
        $q->set('project_id',$p['id']);
        $q->set('name','TestQuote'.time());
        $q->set('amount','50');
        $q->set('rate','40.0');
        $q->set('currency','EUR');
        $q->save();

        $q2 = $this->add('Model_Quote');
        $q2->prepareForSelect($u);
        $q2->load($q['id']);

        $data = $q2->get();

        try {
            $this->assertTrue(isset($data['rate']), 'Finance manager cannot see rate!');
            $this->assertTrue(isset($data['currency']), 'Finance manager cannot see currency!');
            $this->assertTrue(isset($data['calc_rate']), 'Finance manager cannot see rate (calc_rate)!');
            $this->assertTrue(isset($data['estimpay']), 'Finance manager cannot see Est.pay (estimpay)!');

            $q->forceDelete();
            $p->forceDelete();
            $r->delete();
            $u->forceDelete();

        }catch(Exception $e){
            $q->forceDelete();
            $p->forceDelete();
            $r->delete();
            $u->forceDelete();

            throw $e;
        }

    }

    private function atk4_test_can_see_money_false() {
        /**
         * 1. Добавляем юзера
         * 2. НЕ устанавливаем ему право видеть финансы (фу бомжара, наверняка в Таиланде живет)
         * 3. Получаем проекты этого пользователя (прокты, которые может видеть этот пользователь)
         * 4. Проверяем, вернулась ли нам финансовая информация (например, rate)
         * 5. Удаляем юзера и его права
         */

        $u = $this->add('Model_Mock_User');
        $u->set('name','TestUser_'.time());
        $u->save();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($u['id']);
        $r->setRight('can_see_quotes',true);
        $r->save();

        $p = $this->add('Model_Project');
        $p->set('name','TestProject'.time());
        $p->save();

        $q = $this->add('Model_Quote');
        $q->set('project_id',$p['id']);
        $q->set('name','TestQuote'.time());
        $q->set('amount','50');
        $q->set('rate','40.0');
        $q->set('currency','EUR');
        $q->save();

        $q2 = $this->add('Model_Quote');
        $q2->prepareForSelect($u);
        $q2->load($q['id']);

        $data = $q2->get();

        try {
            $this->assertFalse(isset($data['rate']), 'The user is able to see rate!');
            $this->assertFalse(isset($data['currency']), 'The user is able to see currency!');
            $this->assertFalse(isset($data['calc_rate']), 'The user is able to see rate (calc_rate)!');
            $this->assertFalse(isset($data['estimpay']), 'The user is able to see Est.pay (estimpay)!');

            $q->forceDelete();
            $p->forceDelete();
            $r->delete();
            $u->forceDelete();

        }catch(Exception $e){
            $q->forceDelete();
            $p->forceDelete();
            $r->delete();
            $u->forceDelete();

            throw $e;
        }
    }

    private function atk4_test_can_save_money_true() {

        $u = $this->add('Model_Mock_User');
        $u->set('name','TestUser_'.time());
        $u->save();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($u['id']);
        $r->setRight('can_add_quote',true);
        $r->setRight('can_see_finance',true);
        $r->save();

        $p = $this->add('Model_Project');
        $p->set('name','TestProject'.time());
        $p->save();

        $q = $this->add('Model_Quote');
        $q->prepareForInsert($u);
        $q->set('project_id',$p['id']);
        $q->set('name','TestQuote'.time());
        $q->set('amount','30');
        $q->set('rate','40.0');
        $q->set('currency','EUR');
        $q->save();

        $data = $q->get();

        try {
            $this->assertTrue(isset($data['amount']), 'The user cannot set amount!');
            $this->assertTrue(isset($data['rate']), 'The user cannot set rate!');
            $this->assertTrue(isset($data['currency']), 'The user cannot set currency!');

            $q->forceDelete();
            $p->forceDelete();
            $r->delete();
            $u->forceDelete();

        }catch(Exception $e){
            $q->forceDelete();
            $p->forceDelete();
            $r->delete();
            $u->forceDelete();

            throw $e;
        }
    }

    private function atk4_test_can_save_money_false() {

        $u = $this->add('Model_Mock_User');
        $u->set('name','TestUser_'.time());
        $u->save();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($u['id']);
        $r->setRight('can_add_quote',true);
        $r->save();

        $p = $this->add('Model_Project');
        $p->set('name','TestProject'.time());
        $p->save();

        $q = $this->add('Model_Quote');
        $q->prepareForInsert($u);
        $q->set('project_id',$p['id']);
        $q->set('name','TestQuote'.time());
        $q->set('amount','30');
        $q->set('rate','40.0');
        $q->set('currency','EUR');
        $q->save();

        $data = $q->get();

        try {
            $this->assertFalse(isset($data['amount']), 'The user can set amount!');
            $this->assertFalse(isset($data['rate']), 'The user can set rate!');
            $this->assertFalse(isset($data['currency']), 'The user can set currency!');

            $q->forceDelete();
            $p->forceDelete();
            $r->delete();
            $u->forceDelete();

        }catch(Exception $e){
            $q->forceDelete();
            $p->forceDelete();
            $r->delete();
            $u->forceDelete();

            throw $e;
        }
    }
    private function atk4_test_can_delete_quote_true() {

        $u = $this->add('Model_Mock_User');
        $u->set('name','TestUser_'.time());
        $u->save();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($u['id']);
        $r->setRight('can_delete_quote',true);
        $r->save();

        $p = $this->add('Model_Project');
        $p->set('name','TestProject'.time());
        $p->save();

        $q = $this->add('Model_Quote');
        $q->set('project_id',$p['id']);
        $q->set('name','TestQuote'.time());
        $q->save();

        $q->prepareForDelete($u);

        try {
            $q->forceDelete();
            $p->forceDelete();
            $r->delete();
            $u->forceDelete();

        }catch(Exception $e){
            $q->forceDelete();
            $p->forceDelete();
            $r->delete();
            $u->forceDelete();

            $this->fails('User cannot delete quote');
            throw $e;
        }
    }

    /**
     * REQUIREMENTS PERMISSIONS
     */
    private function atk4_test_can_see_requirement_true() {
        $u = $this->add('Model_Mock_User');
        $u->set('name','TestUser_'.time());
        $u->save();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($u['id']);
        $r->setRight('can_see_quotes',true);
        $r->save();

        $p = $this->add('Model_Project');
        $p->set('name','TestProject'.time());
        $p->save();

        $q = $this->add('Model_Quote');
        $q->set('project_id',$p['id']);
        $q->set('name','TestQuote'.time());
        $q->save();

        $req = $this->add('Model_Requirement');
        $req->set('name','TestRequirement'.time());
        $req->set('user_id',$u['id']);
        $req->set('quote_id',$q['id']);
        $req->save();

        $req2 = $this->add('Model_Requirement');
        $req2->prepareForSelect($u);
        $req2->load($req['id']);

        $data = $req2->get();

        try {
            $this->assertTrue(isset($data['name']), 'User cannot see requirement\'s name!');

            $req->forceDelete();
            $q->forceDelete();
            $p->forceDelete();
            $r->delete();
            $u->forceDelete();

        }catch(Exception $e){
            $req->forceDelete();
            $q->forceDelete();
            $p->forceDelete();
            $r->delete();
            $u->forceDelete();

            throw $e;
        }

    }
    private function atk4_test_can_see_requirement_false() {
        $u = $this->add('Model_Mock_User');
        $u->set('name','TestUser_'.time());
        $u->save();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($u['id']);
        $r->save();

        $p = $this->add('Model_Project');
        $p->set('name','TestProject'.time());
        $p->save();

        $q = $this->add('Model_Quote');
        $q->set('project_id',$p['id']);
        $q->set('name','TestQuote'.time());
        $q->save();

        $req = $this->add('Model_Requirement');
        $req->set('name','TestRequirement'.time());
        $req->set('user_id',$u['id']);
        $req->set('quote_id',$q['id']);
        $req->save();

        $req2 = $this->add('Model_Requirement');

        try{
            $this->assertThrowException('Exception_API_CannotSee', $req2, 'prepareForSelect', $args=array($u));

            $req->forceDelete();
            $q->forceDelete();
            $p->forceDelete();
            $r->delete();
            $u->forceDelete();
        }catch (Exception $e){
            $req->forceDelete();
            $q->forceDelete();
            $p->forceDelete();
            $r->delete();
            $u->forceDelete();
            throw $this->exception('User CAN see requirement but not allowed');
        }
    }

    /**
     * USERS PERMISSIONS
     */
    private function atk4_test_can_see_user_true() {
        $u = $this->add('Model_Mock_User');
        $u->set('name','TestUser_'.time());
        $u->save();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($u['id']);
        $r->setRight('can_see_users',true);
        $r->save();

        $u2 = $this->add('Model_User');
        $u2->prepareForSelect($u);
        $u2->load($u['id']);

        $data = $u2->get();

        try {
            $this->assertTrue(isset($data['name']), 'User cannot see user\'s name!');

            $r->delete();
            $u->forceDelete();

        }catch(Exception $e){
            $r->delete();
            $u->forceDelete();

            throw $e;
        }

    }

    /**
     * CLIENTS PERMISSIONS
     */
    private function atk4_test_can_see_clients_true() {
        $u = $this->add('Model_Mock_User');
        $u->set('name','TestUser_'.time());
        $u->save();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($u['id']);
        $r->setRight('can_see_clients',true);
        $r->save();

        $c = $this->add('Model_Client');
        $c->set('name','TestClient_'.time());
        $c->set('email','email_'.time().'@test.com');
        $c->save();

        $c2 = $this->add('Model_Client');
        $c2->prepareForSelect($u);
        $c2->load($c['id']);

        $data = $c->get();

        try {
            $this->assertTrue(isset($data['name']), 'User cannot see client\'s name!');

            $r->delete();
            $c->forceDelete();
            $u->forceDelete();

        }catch(Exception $e){
            $r->delete();
            $c->forceDelete();
            $u->forceDelete();

            throw $e;
        }

    }

    /**
     * DEVELOPER PERMISSIONS
     */
    private function atk4_test_can_see_developers_true() {
        $u = $this->add('Model_Mock_User');
        $u->set('name','TestUser_'.time());
        $u->save();

        $r = $this->add('Model_User_Right');
        $r->saveNewUserAsEmpty($u['id']);
        $r->setRight('can_see_developers',true);
        $r->save();

        $d = $this->add('Model_Developer');
        $d->set('name','TestDeveloper_'.time());
        $d->set('email','email_'.time().'@test.com');
        $d->save();

        $d2 = $this->add('Model_Developer');
        $d2->prepareForSelect($u);
        $d2->load($d['id']);

        $data = $d2->get();

        try {
            $this->assertTrue(isset($data['name']), 'User cannot see developer\'s name!');

            $r->delete();
            $d->forceDelete();
            $u->forceDelete();

        }catch(Exception $e){
            $r->delete();
            $d->forceDelete();
            $u->forceDelete();

            throw $e;
        }

    }
}