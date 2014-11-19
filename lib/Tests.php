<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 03/11/14
 * Time: 22:49
 */
class Tests extends App_TestCLI {

    public $is_test_app = true;

    //use Trait_AddAuth;
    use Trait_AddPathfinder;
//    use Trait_Test_UserRights;
    use Trait_Test_Quote;

    function init() {
        parent::init();
        $this->addPathfinder();
        $this->dbConnect();
    }


}




// http://stackoverflow.com/questions/5265288/update-command-line-output-i-e-for-progress

//echo "Progress :      ";  // 5 characters of padding at the end
//for ($i=0 ; $i<=5 ; $i++) {
//    echo "\033[5D";      // Move 5 characters backward
//    echo str_pad($i, 3, ' ', STR_PAD_LEFT) . " %";    // Output is always 5 characters long
//    sleep(1);           // wait for a while, so we see the animation
//}