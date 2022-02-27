<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService
{
    public function __construct(MailerInterface $mailer, Environment $template)
    {
        $this->mailer   = $mailer;
        $this->template = $template;
    }

    /**
     * 
     */
    public function sendEmail(array $form, $sender, $recipient)
    {
        if (!$this->formVerify($form))
            return false;

        try {
            $email = (new Email())
                ->from($sender)
                ->to($recipient)
                ->subject('New message from '.$form['name'])
                ->text($this->template->render(
                    'email/text.html.twig', [
                        'form' => $form,
                        'date' => new \DateTime()
                    ]
                ))
            ;
            $this->mailer->send($email);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    private function formVerify(array $form)
    {
        foreach ($form as $key => $value)
            if (is_null($value) && $key != 'phone')
                return false;
        return true;
    }
}