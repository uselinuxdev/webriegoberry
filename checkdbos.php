<?php

/* 
 * This php is used to send sumary mail from crontab scheduler
 */

require("InstallClass.php");
// Controlar que exista sesion iniciada
require('adminsession.php');
$_SESSION['pag'] = "checkdbos.php";
//User and password passed by arg.
$userapp = $argv[1];
$passapp = $argv[2];
$phost = "localhost";
if(isset($argv[3]))
{
    $phost = $argv[3];
}
checkuserdb($userapp,$passapp,$phost);

$installclass = new InstallClass();
$installclass->getdbversion(); 

