<?php
ini_set( 'display_errors', 1 );
error_reporting( E_ALL );
$from = "alarmas@riegosolar.net";
$to = "eusebio.antonio.castro@gmail.com";
$subject = "Checking PHP mail";
$message = "PHP mail works just fine";
$header = "From: Gestor de alarmas Riegosolar <".$from.">".PHP_EOL;
mail($to,$subject,$message, $header,"-f$from");
echo "The email message was sent.";
?>
