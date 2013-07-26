<?php
class Controller_Translator extends Controller {
    function __($string) {
        if (array_key_exists($string,$this->translations)) {
            $string = $this->translations[$string];
        }
        /**
         *  Add this line to config.php if you want to see NOT translated strings with smiles
         *   $config['translator']['add_smiles']=true;
         */
        elseif ($this->api->getConfig('translator/add_smiles',false)) {
             $string = '☺'.$string;
        }
        return $string;
    }
    private $translations = array(

        //Meta
        'Adding new Reqcomment'    =>'Adding new Comment',
        
    );
}