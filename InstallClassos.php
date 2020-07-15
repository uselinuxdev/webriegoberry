<?php

/* 
 * This php is used to send sumary mail from crontab scheduler
 */

require("InstallClass.php");
$_SESSION['pag'] = "InstallClassos.php";
session_start();
// Puerto por defecto 3306
$_SESSION['dbport'] = 3306;
//Primero hacemos las conexiones
$_SESSION['serverdb'] = 'localhost';
$_SESSION['dbuser'] = 'riegosql';
$_SESSION['dbpass'] = 'riegoprod15';
$_SESSION['dbname'] = 'riegosolar';
$_SESSION['minsesion'] = 0;

$ClassInstall = new InstallClass();
$ClassInstall->getdbversion();

