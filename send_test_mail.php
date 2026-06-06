<?php

require_once 'config/config.php';

$to = 'joshuajeberson@gmail.com';
$subject = 'ALGORYTHMS Test Mail';
$message = 'SMTP is working successfully';

$headers = "From: noreply@algorythms.in\r\n";
$headers .= "Reply-To: noreply@algorythms.in\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if(mail($to, $subject, $message, $headers)) {
    echo "Mail sent successfully";
} else {
    echo "Mail sending failed";
}