<?php

/* 
 * This php is used to send sumary mail from crontab scheduler
 */
// Controlar que exista sesion iniciada
require('adminsession.php');
require("AlertClass.php");
$_SESSION['pag'] = "mailalertos.php";
//User and password passed by arg.
$userapp = $argv[1];
$passapp = $argv[2];
checkuserdb($userapp,$passapp);

$ClassAlert = new AlertClass();
$ClassAlert->checkalert();

