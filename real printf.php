<?php
$count = 0;
for( $i = 0; $i < 10000; $i++) {
    $time_start = microtime(true);
    printf('J\'achète %d de vos croissants, sinon je vais vous %s le %s bande de %s du %d ième arrondissement', 5, 'claquer', 'derrière', 'fous', 20);
    $count += microtime(true)-$time_start;
}
echo 'Temps d\'execution [fonction printf] : '. (($count/10000)*100).PHP_EOL;