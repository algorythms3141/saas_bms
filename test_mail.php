<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'mail.algorythms.in';
    $mail->SMTPAuth = true;
    $mail->Username = 'noreply@algorythms.in';
    $mail->Password = 'Makebillions@26';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Enable debugging
    $mail->SMTPDebug = 2;

    $mail->setFrom('noreply@algorythms.in', 'ALGORYTHMS');
    $mail->addAddress('joshuajeberson@gmail.com');

    $mail->Subject = 'SMTP Test';
    $mail->Body = 'SMTP working successfully';

    $mail->send();

    echo '<br><br>Mail Sent Successfully';

} catch (Exception $e) {
    echo '<br><br>PHPMailer Error: ' . $mail->ErrorInfo;
}