<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

function envoyerMail($destinataire, $sujet, $corps) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(MAIL_USERNAME, 'Vite & Gourmand');
        $mail->addAddress($destinataire);

        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body    = $corps;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}