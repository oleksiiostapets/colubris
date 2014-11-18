<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 04/11/14
 * Time: 01:30
 */
trait Trait_AddPathfinder {
    function addPathfinder() {
        $this->pathfinder->addLocation(array(
            'addons'=>array('./atk4-addons','./addons','./vendor'),
            'php'=>array('./shared','./shared/lib'),
        ))->setBasePath('.');
    }
}