<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * @param string[] $recipients
 * @param string $subject
 * @param string $message
 * @return void
 * @throws Exception
 */
function sendMail(array $recipients, string $subject, string $message): void
{
    $mailer = createMailer();
    foreach ($recipients as $email) {
        $mailer->addAddress($email);
    }
    $mailer->Subject = $subject;
    $mailer->Body = $message;
    $mailer->send();
}

/**
 * @param string[] $recipients
 * @param string $subject
 * @param string $headline
 * @param string $message
 * @return void
 * @throws Exception
 */
function sendGenericMail(array $recipients, string $subject, string $headline, string $message): void
{
    sendMail($recipients, $subject, view('mail/GenericMail', ['headline' => $headline, 'message' => $message]));
}

/**
 * Create a new mailer instance.
 *
 * @return PHPMailer
 * @throws Exception
 */
function createMailer(): PHPMailer
{
    $mailer = new PHPMailer();

    $mailer->isSMTP();
    $mailer->Host = getenv('mail.host');
    $mailer->SMTPAuth = true;
    $mailer->Username = getenv('mail.username');
    $mailer->Password = getenv('mail.password');
    $mailer->SMTPSecure = 'tls';
    $mailer->Port = 587;

    $mailer->setFrom(getenv('mail.from.address'), getenv('mail.from.name'));
    $mailer->isHTML();
    $mailer->CharSet = PHPMailer::CHARSET_UTF8;

    return $mailer;
}