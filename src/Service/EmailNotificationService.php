<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;

class EmailNotificationService extends AbstractService
{
    public function sendEmail(string $receiver, string $case): ?string
    {
        try {
            $email = (new TemplatedEmail())
                ->from('kakarot@codexpress.fr')
                ->to($receiver)
                ->subject($case['subject'])
                ->priority(Email::PRIORITY_HIGH)
                ->htmlTemplate('email/'. $case['template'] .'.html.twig')
                ;
            $this->mailer->send($email);
            return 'The e-mail was sucessfully sent!';
        } catch (\Exception $e) {
            return 'An error occurred while sending the e-mail: ' . $e->getMessage();
        }

    }
}