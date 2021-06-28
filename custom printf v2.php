<?php

namespace customPrint;

use Exception;

$time_start = microtime(true);
printf('J\'achète %d de vos croissants, sinon je vais vous %s le %s bande de %s.', 5, 'péter', 'cul', 'merdes');
$time_end = microtime(true);
echo 'Temps d\'execution [fonction printf] : '. (($time_end-$time_start)*100).PHP_EOL;

//0.0034  3.4ms d'execution pour le vrai printf
//0.0121  12.1ms d'execution pour le custom v1
//0.0112  11.2ms d'execution pour le custom v2

function printf(string $text, mixed ... $args) {

    $time_print_start = microtime(true);
    $nbValues = 0;
    $argumentPossibles = ['b','c','d','e','E','f','g','G','h','H','o', 's', 'u', 'x', 'X'];
    $argumentFoundPlace = array();
    foreach($argumentPossibles as $value)
    {
        //echo '%'.$value.PHP_EOL;
        $count = substr_count($text, '%'.$value);
        $countWhile = $count;
        if($count > 0) {
            $nbValues += $count;
            //echo 'nbvalues = '.$nbValues.PHP_EOL;
            //echo '%'.$value. ' trouvé '.$count. ' fois.'.PHP_EOL;
            $posIndex = 0;

            while($countWhile > 0)
            {
                //echo 'Test posIndex = '. $posIndex.PHP_EOL;
                $pos = strpos($text, '%'.$value, $posIndex);
                if($pos !== false) {
                    $argumentFoundPlace[] = ['value' => '%'.$value, 'pos' => $pos];
                    if($pos+2 < strlen($text)) $posIndex = $pos+2;
                    else break;
                    $countWhile--;
                }
            }
        }
    }
    if($nbValues != count($args)) {
        echo 'ERREUR, nombre de paramètre incorrect par rapport aux variables renseignés [nbValues='.$nbValues.', count ars = '.count($args).PHP_EOL;
        die();
    }
    //$time_end = microtime(true);
    //echo 'Temps d\'execution [foreach argumentPossibles] : '. (($time_end-$time_start)*100).PHP_EOL;
    //$time_start = microtime(true);
    usort($argumentFoundPlace, function ($a, $b) {
        if ($a['pos'] == $b['pos']) {
            return 0;
        }
        return ($a['pos'] < $b['pos']) ? -1 : 1;
    });
    //$time_end = microtime(true);
    //echo 'Temps d\'execution [usort()] : '. (($time_end-$time_start)*100).PHP_EOL;
    //$time_start = microtime(true);
    /*foreach($argumentFoundPlace as $key => $value)
    {
        echo '[index = '.$key.', Value = '. $value['value']. ', Position : '. $value['pos'].']'.PHP_EOL;
    }*/
    $countStrAugment = 0;
    for($i = 0; $i < count($argumentFoundPlace); $i++) {
        //echo '------------------------------------------'.PHP_EOL;
        $tag = $argumentFoundPlace[$i]['pos'];
        //echo '[index = '.$i. ', texte= '.$textFirstSplit.PHP_EOL.']';
        //echo '[suite string = "'.$textSecondSplit.'"'.PHP_EOL;
        //echo '[index testé ='.$i.' , value testé = '. $argumentFoundPlace[$i]['value'].', args même index = '.$args[$i].']'.PHP_EOL;
        switch($argumentFoundPlace[$i]['value'])
        {
            case '%s':
            {
                break;
            }
            case '%b': {
                if(is_int($args[$i])) {
                    $args[$i] = decbin($args[$i]);
                }
                else throw new Exception('Erreur de type');
                break;
            }
            //case '%u':
            //case '%c':
            //case '%o':
            //case '%x':
            //case '%X':
            case '%d':
            {
                if (is_int($args[$i])) {
                    $args[$i] = (string)$args[$i];
                }
                else throw new Exception('Erreur de type');
                break;
            }
            //case '%e':
            //case '%E':
            //case '%F':
            //case '%g':
            //case '%G':
            //case '%h':
            //case '%H':
            case '%f':
            {
                if(is_float($args[$i])) {
                    $args[$i] = (string)$args[$i];
                }
                else throw new Exception('Erreur de type');
                break;
            }
        }
        $text = substr_replace($text, $args[$i], $tag+$countStrAugment, 2);
        $countStrAugment += (strlen($args[$i])-2);
    }
    echo $text.PHP_EOL;
    $time_print_end = microtime(true);
    echo 'Temps d\'execution [printf intérieur total] : '. (($time_print_end-$time_print_start)*100).PHP_EOL;
}