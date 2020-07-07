<?php

/* 
 * This php is used to send sumary mail from crontab scheduler
 */

require("riegoresumenClass.php");
// Controlar que exista sesion iniciada
require('adminsession.php');
require("AlertClass.php");
$_SESSION['pag'] = "mailsumaryos.php";
//User and password passed by arg.
$userapp = $argv[1];
$passapp = $argv[2];
$phost = "localhost";
if(isset($argv[3]))
{
    $phost = $argv[3];
}
checkuserdb($userapp,$passapp,$phost);

$Classresprod = new riegoresumenClass();
$Classresprod->cargarClase('resumenprod'); 
$asumaryprod = $Classresprod->calcsumaryprod();
$ClassAlert = new AlertClass();
$ClassAlert->mailsumary($asumaryprod);

