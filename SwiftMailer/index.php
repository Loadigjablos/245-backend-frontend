<?php

require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/autoload.php';
$mail = new PHPMailer\PHPMailer\PHPMailer();
use PHPMailer\PHPMailer\SMTP;
// Disable SSL verification
stream_context_set_default(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);


$mail->isSMTP();
$mail->Host = 'mail.wiroute.com';
$mail->SMTPAuth = true; // Enable SMTP authentication
$mail->Username = 'fovegij816@wiroute.com'; // SMTP username
$mail->SMTPSecure = 'tls'; // Enable SSL encryption, `tls` also accepted
$mail->Port = 465; // TCP port to connect to

/*$mail->SMTPOptions = array(
  'ssl' => array(
      'verify_peer' => false,
      'verify_peer_name' => false,
      'allow_self_signed' => true
  )
);
*/

$mail->setFrom('fovegij816@wiroute.com', 'Mouayad'); // Set the sender's email address and name
$mail->addAddress('fovegij816@wiroute.com', 'Mouayad'); // Set the recipient's email address and name
$mail->isHTML(true); // Set email format to HTML
$mail->Subject = 'Test Email'; // Set the subject of the email
$mail->Body = 'This is a test email.'; // Set the body of the email

$mail->SMTPDebug = SMTP::DEBUG_CONNECTION;

if (!$mail->send()) {
  echo 'Message could not be sent.';
  echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
  echo 'Message has been sent';
}

?>
