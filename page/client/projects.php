<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 7/9/13
 * Time: 5:35 PM
 * To change this template use File | Settings | File Templates.
 */
class page_client_projects extends Page {
    function init(){
   		parent::init();
        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Projects',
                    'url' => 'client/projects',
                ),
            )
        ),'bread_crumb');
    }
    function defaultTemplate(){
        return array('page/client/projects');
    }
}