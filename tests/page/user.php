<?php
/**
 *Created by Konstantin Kolodnitsky
 * Date: 04.04.14
 * Time: 12:49
 */
class page_user extends Page_Tester{
	public $proper_responses=array(
		"Test_createUser"=>'OK',
	);
	function test_createUser(){
		return 'Fail';
	}
}