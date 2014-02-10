<?php
/**
 *Created by Konstantin Kolodnitsky
 * Date: 08.02.14
 * Time: 0:34
 */
class Layout_Colubris extends Layout_Basic{
    function init(){
        parent::init();

        $this->rm = $this->add('RoleMenu', 'SubMenu', 'SubMenu');
        $this->add('MyMenu',null,'Main_Menu');
        //$this->add('MySubMenu', 'SubMenu', 'SubMenu');

        // show current user name
        $this->template->set('name',$this->api->auth->model['name']?$this->api->auth->model['name']:'Guest' . ' @ ' .'Colubris Team Manager, ver.'.$this->api->getVer())
        ;

        $this->template->trySet('year',date('Y',time()));

        $this->api->defineAllowedPages();

        try {
            if(!$this->api->auth->isPageAllowed($this->api->page)){
                throw $this->exception('This user cannot see this page','Exception_Denied');
            }
        } catch (Exception_Denied $e) {
            // TODO show denied page
            //throw $e;
            $v = $this->add('View')->addClass('denied');
            $v->add('View')->setElement('h2')->set('You cannot see this page');
            $v->add('View_Error')->set('Try to change role if you have multiple roles for this account');
        }

        $this->addFooter()
            ->setHTML('
            <div class="row atk-wrapper">
                <div class="col span_8">
                    This system is implemented using Agile Toolkit. © 1999–2014. See <a href="http://agiletoolkit.org/about/license" target="_blank">License</a>
                </div>
                <div class="col span_4 atk-align-center">
                    <img src="'.$this->api->pm->base_path.'images/powered_by_agile.png" alt="powered_by_agile">
                </div>
            </div>
        ');
    }
//    function defaultTemplate() {
//        return array('layout/fluid');
//    }
}