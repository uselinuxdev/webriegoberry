<?php

/* 
 * This php is used to send sumary mail from crontab scheduler
 */

// Controlar que exista sesion iniciada
require('adminsession.php');
require("ExportClass.php");
$_SESSION['pag'] = "ExportEvent.php";
//User and password passed by arg.
$userapp = $argv[1];
$passapp = $argv[2];
$phost = "localhost";
if(isset($argv[3]))
{
    $phost = $argv[3];
}
checkuserdb($userapp,$passapp,$phost);

$ClassExport = new ExportClass();
$fcalc = $argv[4];
if(!isset($argv[4]))
{
    //$fcalc = '2019-09-03';
    $fcalc=date('Y-m-d');
}
//echo 'Fecha de calculo Evento:'.$fcalc;
$ClassExport->GenCalcExp($fcalc);

