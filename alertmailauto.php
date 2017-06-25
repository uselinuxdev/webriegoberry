#!/usr/bin/php
<?php
/* 
PHP permite llamar a la clase de alertas desde un proceso externo (CRON/JOB MySQL) para realizar el check de alertas
 */
// loop through each element in the $argv array
$aoption = array();
$aoption[0]='A';
$aoption[1]='E';

//var_dump($argv);

if(count($argv) <> 2 or in_array($argv[1], $aoption)==FALSE) {
    echo "Debe de indicar el tipo de informe que desea generar. \n";
    echo "----------------------------------------------------- \n";
    echo "A - Informe de alertas\n";
    echo "E - Informe de alertas de valores estimados \n";
    return 1;
}
// Si llega esque tiene uno de los 2 argumentos, lanzar check:
//Primero hacemos las conexiones
session_start();
$_SESSION['serverdb'] = 'localhost';
$_SESSION['dbuser'] = 'riegosql';
$_SESSION['dbpass'] = 'riegoprod15';
$_SESSION['dbname'] = 'riegosolar';
$_SESSION['minsesion'] = 0;
require("AlertClass.php");
$ClassAlert = new AlertClass();
switch ($argv[1]) {
    case 'A':
        $ClassAlert->checkalert();
        break;
    case 'E':
        $ClassAlert->checkstimate();
        break;
}
?>