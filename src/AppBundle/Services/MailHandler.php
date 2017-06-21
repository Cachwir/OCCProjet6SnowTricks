<?php
/**
 * Created by PhpStorm.
 * User: cachwir
 * Date: 21/06/17
 * Time: 17:22
 */

namespace AppBundle\Services;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Twig\Environment;

class MailHandler implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function send($subject, $body, $to)
    {
        $mailer = $this->container->get('swiftmailer.mailer');

        $message = Swift_Message::newInstance()
            ->setSubject($subject)
            ->setTo($to)
            ->setBody($body)
            ->setContentType("text/html")
        ;

        try {
            $result = $mailer->send($message);
            if (!$result) {
                throw new \Exception("Une erreur est survenue lors de l'envoi du mail. Réessayez dans quelques minutes et contactez un administrateur si le problème persiste.");
            }
        } catch (\Exception $e) {
            $result = $e->getMessage();
        }

        return $result;
    }

    public function sendPasswordReinitialisationMail(User $to)
    {
        $subject = "SnowTricks : réinitialisation de ton mot de passe";
        $message = $this->container->get('templating')->render("AppBundle:front/mails:passwordReinitialisationMail.html.twig", ['user' => $to]);

        return $this->send($subject, $message, $to->getEmail());
    }
}