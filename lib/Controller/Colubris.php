<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 8/29/13
 * Time: 12:37 PM
 * To change this template use File | Settings | File Templates.
 */
class Controller_Colubris extends AbstractController {

    function makeUrls($text) {
        preg_match_all(
            '/((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)/',
            $text,$matches
        );

        $replaced = array();
        if (isset($matches[0])) {
            foreach ($matches[0] as $match) {
                if (in_array($match,$replaced)) continue;
                $text = str_replace($match,'<a href="'.$match.'" target="_blank">'.$match.'</a>',$text);
                $replaced[] = $match;
            }
        }
        return $text;
    }

}