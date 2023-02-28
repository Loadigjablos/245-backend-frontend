<?php
// Empfänger-Adresse
$to = "morhaf.mouayad@gmail.com";

// Betreff der E-Mail
$subject = "Neue Nachricht von " . $_POST['name'];

// Nachrichteninhalt
$message = "Name: " . $_POST['name'] . "\r\n";
$message .= "E-Mail: " . $_POST['email'] . "\r\n";

// Header-Informationen
$headers = "From: mouayad.alnhlawe@ict.csbe.ch" . "\r\n";
$headers .= "Reply-To: morhaf.mouayad@gmail.com" . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

ini_set('SMTP', 'localhost');
ini_set('smtp_port', 587);

// E-Mail senden
if(mail($to, $subject, $message, $headers)){
    // Setzen des SMTP-Servers und des Ports für die E-Mail-Versand
    echo "Die E-Mail wurde erfolgreich gesendet.";
} else {
    echo "Die E-Mail konnte nicht gesendet werden.";
}


?>
