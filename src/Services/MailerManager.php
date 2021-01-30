<?php

namespace App\Services;

use App\Renderer\Renderer;

class MailerManager {

    /**
     * @var Renderer
     */
    private $renderer;

    public function __construct()
    {
        $this->renderer = new Renderer();
    }

    public function sendMail($message) {
        // Create the Transport
        $transport = (new \Swift_SmtpTransport('0.0.0.0', 1025));
        // Create the Mailer using your created Transport
        $mailer = new \Swift_Mailer($transport);
        // Create a message

        // Send the message
        return $mailer->send($message);
    }

    public function sendContact($values) {

        $body = $this->renderer->display("mails/contact.html.twig", ['contact' => $values]);

        $message = (new \Swift_Message('Nouvelle demande de contact !'))
            ->setFrom(['no-reply@axelvllr.com' => 'AxelVllR Blog'])
            ->setTo(['name@domain.org'])
            ->setBody($body, 'text/html')
        ;

        $this->sendMail($message);
    }
}