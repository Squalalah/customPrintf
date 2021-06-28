<?php

namespace customPrint;

use Exception;

$time_start = microtime(true);
printf('J\'achète %d de vos croissants, sinon je vais vous %s le %s bande de %s du %d ième arrondissement', 5, 'claquer', 'derrière', 'fous', 20);
$time_end = microtime(true);
echo 'Temps d\'execution [fonction printf] : '. (($time_end-$time_start)*100).PHP_EOL;

//0.0034  3.4ms d'execution pour le vrai printf
//0.0121  12.1ms d'execution pour le custom v1
//0.0109  10.9ms d'execution pour le custom v2
//0.0105  10.5ms d'execution pour le custom v3
//0.0088   8.8ms d'execution pour le custom v4 (moyenne à 9ms)

function printf(string $text, mixed ... $args) {

    $time_print_start = microtime(true);
    $argumentPossibles = ['b','c','d','e','E','f','g','G','h','H','o', 's', 'u', 'x', 'X'];
    $argumentFoundPlace = array();
    $posIndex = 0;
    while(($pos = strpos($text, '%', $posIndex)) !== false)
    {
        if($posIndex+1 < strlen($text)) {
            if(in_array($text[$pos+1],$argumentPossibles))
            {
                $argumentFoundPlace[] = ['value' => '%'.$text[$pos+1], 'pos' => $pos];
            }
            $posIndex = $pos+2;
        }
    }
    /*foreach($argumentFoundPlace as $key => $value)
    {
        echo '[index = '.$key.', Value = '. $value['value']. ', Position : '. $value['pos'].']'.PHP_EOL;
    }*/
    $nbValues = count($argumentFoundPlace);
    if($nbValues != count($args)) {
        echo 'ERREUR, nombre de paramètre incorrect par rapport aux variables renseignés [nbValues='.$nbValues.', count args = '.count($args).']'.PHP_EOL;
        die();
    }
    /*$time_end = microtime(true);
    echo 'Temps d\'execution [foreach argumentPossibles] : '. (($time_end-$time_start)*100).PHP_EOL;
    $time_start = microtime(true);
    usort($argumentFoundPlace, function ($a, $b) {
        if ($a['pos'] == $b['pos']) {
            return 0;
        }
        return ($a['pos'] < $b['pos']) ? -1 : 1;
    });
    $time_end = microtime(true);
    echo 'Temps d\'execution [usort()] : '. (($time_end-$time_start)*100).PHP_EOL;
    $time_start = microtime(true);
    foreach($argumentFoundPlace as $key => $value)
    {
        echo '[index = '.$key.', Value = '. $value['value']. ', Position : '. $value['pos'].']'.PHP_EOL;
    }*/
    $countStrAugment = 0;
    $highestChanged = 0;
    for($i = 0; $i < $nbValues; $i++) {
        //echo '------------------------------------------'.PHP_EOL;
        $tag = $argumentFoundPlace[$i]['pos'];
        /*echo '[index = '.$i. ', texte= '.$textFirstSplit.PHP_EOL.']';
        echo '[suite string = "'.$textSecondSplit.'"'.PHP_EOL;
        echo '[index testé ='.$i.' , value testé = '. $argumentFoundPlace[$i]['value'].', args même index = '.$args[$i].']'.PHP_EOL;
        */
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
        if($tag > $highestChanged) {
            $text = substr_replace($text, $args[$i], $tag+$countStrAugment, 2);
            $highestChanged = $tag;
        }
        else {
            $text = substr_replace($text, $args[$i], $tag, 2);
        }
        $countStrAugment += (strlen($args[$i])-2);
    }
    echo $text.PHP_EOL;
    $time_print_end = microtime(true);
    echo 'Temps d\'execution [printf intérieur total] : '. (($time_print_end-$time_print_start)*100).PHP_EOL;
}