<?php
use PHPMailer\PHPMailer\PHPMailer;
require '../vendor/autoload.php';
$mail = new PHPMailer;
$mail->isSMTP();
$mail->SMTPDebug = 2;
$mail->Host = 'smtp.riegosolar.net';
$mail->Port = 587;
$mail->SMTPAuth = true;
$mail->Username = 'alertas@riegosolar.net';
$mail->Password = 'Riegosolar77';
$mail->setFrom('alertas@riegosolar.net', 'Alertas automaticas');
$mail->addAddress('eusebio.antonio.castro@gmail.com', 'Receptor de alertas');
$mail->Subject = 'PHPMailer SMTP message';
$mail->msgHTML(file_get_contents('ejemplojava.html'), __DIR__);
$mail->AltBody = 'This is a plain text message body';
if (!$mail->send()) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message sent!';
}
?>
