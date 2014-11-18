<?php
/**
 * Created by PhpStorm.
 * User: alf
 * Date: 11/17/14
 * Time: 2:26 PM
 */

trait Trait_Test_UserRights {

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
    /**
     *
     */
}