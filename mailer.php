<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/autoload.php';

function envoyerMail($destinataire, $sujet, $corps) {
    $mail = new PHPMailer(true);

    try {
        //Configuration du serveur
        $mail->isSMTP();
        $mail->Host         ='smtp.gmail.com';
        $mail->SMTPAuth     =true;
        $mail->Host         ='vite.gourmand.contact@gmail.com';
        $mail->Host         ='mnilrrnfsmwkvjyk';
        $mail->Host         ='PHPMailer::ENCRYPTION_STARTTLS';
        $mail->Host         ='587';
        $mail->Host         ='UTF_8';

        // Expéditeur et destinataire
        $mail->setFrom('vite.gourmand.contact@gmail.com' , 'Vite & Gourmand');
        $mail->addAddress($destinataire);

        //Contenu du mail 
        $mail->isHTML(true);
        $mail->Subject  = $sujet;
        $mail->Body     =$corps;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}