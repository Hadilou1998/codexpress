<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotificationService
{
    public function __construct(private MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    
    public function sendEmail(string $receiver): ?string
    {
        try {
            $email = (new TemplatedEmail())
                ->from('kakarot@codexpress.fr')
                ->to($receiver)
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Time for Symfony Mailer!')
                ->htmlTemplate('email/base.html.twig');

            $this->mailer->send($email);
            return 'Email was successfully sent!';
        } catch (\Exception $e) {
            return 'An error occurred while sending the email: ' . $e->getMessage();
        }
    }
}
?>