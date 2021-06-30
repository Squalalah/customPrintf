<?php

namespace customPrint;

use Exception;

$time_start = microtime(true);
printf('J\'achète %d de vos croissants, sinon je vais vous %s le %s bande de %s du %d ième arrondissement', 5, 'claquer', 'derrière', 'fous', 20);
$time_end = microtime(true);
echo 'Temps d\'execution [fonction printf] : '. (($time_end-$time_start)*100).PHP_EOL;

//0.0036  3.6ms d'execution en moyenne (1000 tests) pour le printf d'origine
//0.0066  6.6ms d'execution en moyenne (1000 tests) pour le custom v1
//0.0060  6.0ms d'execution en moyenne (1000 tests) pour le custom v2
//0.0025  2.5ms d'execution en moyenne (1000 tests) pour le custom v3
//0.0016  1.6ms d'execution en moyenne (1000 tests) pour le custom v4 (toutes les valeurs ne sont pas encore géré, contrairement au printf original)

//0.0034  3.4ms d'execution pour le vrai printf
//0.0121  12.1ms d'execution pour le custom v1
//0.0109  10.9ms d'execution pour le custom v2
//0.0105  10.5ms d'execution pour le custom v3
//0.0082   8.2ms d'execution pour le custom v4 (moyenne à 8.6ms)

function printf(string $text, mixed ... $args) {

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
    $nbValues = count($argumentFoundPlace);
    if($nbValues != count($args)) throw new Exception('ERREUR, nombre de paramètre incorrect par rapport aux variables renseignés [nbValues = '.$nbValues.', count args = '.count($args).']');
    $countStrAugment = 0;
    for($i = 0; $i < $nbValues; $i++) {
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
                else throw new Exception('Nombre entier attendu au paramètre '.$i);
                break;
            }
            case '%c': {
                if(is_int($args[$i]))
                {
                    if(0 <= $args[$i] && $args[$i] <= 255) {
                        $args[$i] = chr($args[$i]);
                    }
                    else $args[$i] = chr(255);
                }
                else throw new Exception('Nombre entier attendu au paramètre '.$i);
                break;
            }
            case '%o': {
                if (is_int($args[$i])) {
                    $args[$i] = decoct($args[$i]);
                }
                else throw new Exception('Nombre entier attendu au paramètre '.$i);
                break;
            }
            case '%d':
            {
                if (is_int($args[$i])) {
                    $maxSigned = pow(2, 32)*2;
                    if(($args[$i]) < 0) {
                        $args[$i] = $maxSigned+$args[$i];
                    }
                    elseif ($args[$i] > $maxSigned)
                    {
                        $args[$i] = $args[$i]-$maxSigned;
                    }
                }
                else throw new Exception('Nombre entier attendu au paramètre '.$i);
                break;
            }
            //case '%x':
            //case '%X':
            //case '%e':
            //case '%E':
            //case '%F':
            //case '%g':
            //case '%G':
            //case '%h':
            //case '%H':
            case '%f':
            {
                if(!is_float($args[$i])) throw new Exception('Nombre démical attendu au paramètre '.$i);
                break;
            }
        }
        $text = substr_replace($text, (string)$args[$i], $argumentFoundPlace[$i]['pos']+$countStrAugment, 2);
        $countStrAugment += (strlen($args[$i])-2);
    }
    echo $text.PHP_EOL;
}