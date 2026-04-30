<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'vite.gourmand.contact@gmail.com';
    $mail->Password   = 'mnilrrnfsmwkvjyk';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('vite.gourmand.contact@gmail.com', 'Vite & Gourmand');
    $mail->addAddress('vite.gourmand.contact@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = 'Test Vite & Gourmand';
    $mail->Body    = '<h1>Ça marche !</h1>';

    $mail->send();
    echo 'Mail envoyé !';

} catch (Exception $e) {
    echo 'Erreur : ' . $mail->ErrorInfo;
}