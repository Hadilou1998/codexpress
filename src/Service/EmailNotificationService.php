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
    
    public function sendEmail(string $receiver, string $case): ?string
    {
        try {
            $email = (new TemplatedEmail()) // Email sans template
                ->from('kakarot@codexpress.fr') // Adresse de l'expéditeur
                ->to($receiver) 
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                ->htmlTemplate('email/base.html.twig'); // Template HTML

                if ($case === 'premium') {
                    $email
                        ->subject('Thank you for your purchase!')
                        ->priority(Email::PRIORITY_HIGH)
                        ->htmlTemplate('email/premium.html.twig')
                    ;
                } elseif ($case === 'registration') {
                    $email
                        ->subject('Welcome to CodeXpress, explore a new way of sharing code')
                        ->htmlTemplate('email/welcome.html.twig')
                    ;
                }

            $this->mailer->send($email);
            return 'Email was successfully sent!';
        } catch (\Exception $e) {
            return 'An error occurred while sending the email: ' . $e->getMessage();
        }
    }
}
?>