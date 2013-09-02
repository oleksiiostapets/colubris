<?php
class RoleMenu extends CompleteLister {
    private $session_name = 'current_user_role';
    private $current_user_posible_roles = array();
    private $current_role = null;
    function init() {
        parent::init();
        $this->addClass('role-menu-lister');
        $this->defineCurrentRole();
        $this->checkPOST();
        $source = $this->addMenuItems();
        $this->setSource($source);
    }
    function formatRow() {
        parent::formatRow();
        $this->current_row_html['name'] =
                '<div id="role_menu_'
                    .$this->current_row['id']
                    .'" class="a_look role_menu '.($this->isCurrent()?'current_role':'')
                    .'">'.$this->current_row['label']
                .'</div>';
        $this->js(true)->colubris()->roleMenuClick(
            'role_menu_'.$this->current_row['id'],
            $this->api->url('/'),
            array(
                'new_role' => $this->current_row['name'],
                'redir'    => $this->api->url()
            )
        );
    }
    function isCurrent() {
        return ($this->current_row['name'] == $this->getCurrentUserRole());
    }
    function getCurrentUserRole() {
        return $this->api->recall($this->session_name);
    }


    private function defineCurrentRole() {
        if ($this->api->auth->isLoggedIn()) {
            // get all posible roles for current user
            if ($this->api->currentUser()->isAdmin())     $this->current_user_posible_roles[] = 'admin';
            if ($this->api->currentUser()->isManager())   $this->current_user_posible_roles[] = 'manager';
            if ($this->api->currentUser()->isDeveloper()) $this->current_user_posible_roles[] = 'developer';
            if ($this->api->currentUser()->isClient())    $this->current_user_posible_roles[] = 'client';

            // in case if there is no any role for this user
            if (!count($this->current_user_posible_roles)) throw $this->exception('You don\'t have any role in system :(');

            $session_role = $this->getCurrentUserRole();

            if (!in_array($session_role,$this->current_user_posible_roles)) {
                $this->setRole($this->current_user_posible_roles[0]);
            }
        }
    }
    private function setRole($role) {
        $this->current_role = $role;
        $this->api->memorize($this->session_name, $role);
    }
    private function addMenuItems() {
        $source = array();
        $count = 0;
        foreach ($this->current_user_posible_roles as $role) {
            $source[] = array(
                'id'   => $count++,
                'name' => $role,
                'label' => ucwords($role),
            );
        }
        return $source;
    }
    private function checkPOST() {
        if ($_POST['new_role']) {
            if (!in_array($_POST['new_role'],$this->current_user_posible_roles)) {
                throw $this->exception('There is no such a role for You');
            } else {
                $this->setRole($_POST['new_role']);
                $this->api->js(null,"$(location).attr('href', '".$this->api->url()."');")->execute();
                //$this->api->js(null,"$(location).attr('href', '".$_POST['redir']."');")->execute();
            }
        }
    }
}