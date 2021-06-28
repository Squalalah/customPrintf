<?php

namespace customPrint;

$time_start = microtime(true);
printf('J\'achète %d de vos croissants, sinon je vais vous %s le %s bande de %s.', 5, 'péter', 'cul', 'merdes').PHP_EOL;
$time_end = microtime(true);
echo 'Temps d\'execution : '. (($time_end-$time_start)*100);

//0.0121  12.1ms d'execution pour le custom
//0.0034 3.4ms d'execution pour le vrai printf

function printf(string $text, mixed ... $args) {

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
    unset($nbValues);
    unset($argumentPossibles);
    usort($argumentFoundPlace, function ($a, $b) {
        if ($a['pos'] == $b['pos']) {
            return 0;
        }
        return ($a['pos'] < $b['pos']) ? -1 : 1;
    });
    /*foreach($argumentFoundPlace as $key => $value)
    {
        echo '[index = '.$key.', Value = '. $value['value']. ', Position : '. $value['pos'].']'.PHP_EOL;
    }*/
    $countStrAugment = 0;
    for($i = 0; $i < count($argumentFoundPlace); $i++) {
        //echo '------------------------------------------'.PHP_EOL;
        $tag = $argumentFoundPlace[$i]['value'];
        $textFirstSplit = substr($text, 0, $argumentFoundPlace[$i]['pos']+2+$countStrAugment);
        $textSecondSplit = substr($text, $argumentFoundPlace[$i]['pos']+2+$countStrAugment, strlen($text));
        //echo '[index = '.$i. ', texte= '.$textFirstSplit.PHP_EOL.']';
        //echo '[suite string = "'.$textSecondSplit.'"'.PHP_EOL;
        //echo '[index testé ='.$i.' , value testé = '. $argumentFoundPlace[$i]['value'].', args même index = '.$args[$i].']'.PHP_EOL;
        switch($tag)
        {
            case '%s':
            {
                $textFirstSplit = str_replace($tag, $args[$i], $textFirstSplit);
                $countStrAugment += (strlen($args[$i])-2);
                $text = $textFirstSplit.$textSecondSplit;
                break;
            }
            case '%d':
            case '%u':
            case '%c':
            case '%o':
            case '%x':
            case '%X':
            case '%b': {
                if(is_int($args[$i])) {
                    $textFirstSplit = str_replace($tag, $args[$i], $textFirstSplit);
                    $text = $textFirstSplit.$textSecondSplit;
                    $countStrAugment += (strlen((string)$args[$i])-2);

                }
            }
            case '%e':
            case '%E':
            case '%f':
            case '%F':
            case '%g':
            case '%G':
            case '%h':
            case '%H': {
                if(is_float($args[$i])) {
                    $textFirstSplit = str_replace($tag, $args[$i], $textFirstSplit);
                    $text = $textFirstSplit.$textSecondSplit;
                    $countStrAugment += (strlen((string)$args[$i])-2);
                }
            }
        }
    }
    echo $text;
}