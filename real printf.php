<?php
$time_start = microtime(true);
printf('J\'achète %d de vos croissants, sinon je vais vous %s le %s bande de %s.', 5, 'claquer', 'derrière', 'fous');
$time_end = microtime(true);
echo 'Temps d\'execution : '. (($time_end-$time_start)*100);