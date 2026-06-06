<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    public static function send($to, $subject, $body)
    {
        try {

            $mail = new PHPMailer(true);

            $mail->isSMTP();
			$mail->SMTPOptions = [
				'ssl' => [
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				]
			];
            $mail->Host = 'server.ayiarak.in';
            $mail->SMTPAuth = true;
            $mail->Username = 'noreply@algorythms.in';
            $mail->Password = 'Makebillions@26';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom(
                'noreply@algorythms.in',
                'ALGORYTHMS'
            );

            $mail->addAddress($to);

            $mail->isHTML(true);

            $mail->Subject = $subject;
            $mail->Body = $body;

            return $mail->send();

        } catch (Exception $e) {

            error_log(
                'Mail Error: ' .
                $mail->ErrorInfo
            );

            return false;
        }
    }
}