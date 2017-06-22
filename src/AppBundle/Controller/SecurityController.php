<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\ForgotPasswordForm;
use AppBundle\Form\LoginForm;
use AppBundle\Form\ReinitialisePasswordForm;
use AppBundle\Form\UserRegistrationForm;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    public function loginAction(Request $request, AuthenticationUtils $authUtils, EntityManagerInterface $em)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        $connectionForm = $this->createForm(LoginForm::class, [
            '_username' => $lastUsername,
        ]);

        $forgotPasswordForm = $this->createForm(ForgotPasswordForm::class);

        $forgotPasswordForm->handleRequest($request);

        if ($forgotPasswordForm->isSubmitted() && $forgotPasswordForm->isValid()) {

            $email = $forgotPasswordForm->getData()['email'];

            $user = $em->getRepository('AppBundle:User')
                ->findOneBy(['email' => $email]);

            if (!$user instanceof User) {
                throw new UsernameNotFoundException();
            }

            $user->generateReinitialisationToken();

            // sends an email
            $result = $this->get("app.mailer")->sendPasswordReinitialisationMail($user);

            if ($result === 1) {
                $em->persist($user);
                $em->flush();

                $this->addFlash(
                    'success',
                    sprintf('Un email a été envoyé à %s. Suis ses instructions pour réinitialiser ton mot de passe.', $email)
                );
            } else {
                $error = $result;
            }
        }

        return $this->render('AppBundle:front:login.html.twig', array(
            'connectionForm' => $connectionForm->createView(),
            'forgotPasswordForm' => $forgotPasswordForm->createView(),
            'error'         => $error,
        ));
    }

    public function logoutAction()
    {
        throw new \Exception('this should not be reached!');
    }

    public function registerAction(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(UserRegistrationForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                sprintf('Bienvenue dans la cour des grands, %s !', $user->getPseudonym())
            );

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
        }

        return $this->render('AppBundle:front:register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function reinitialisePasswordAction(User $user, $token, Request $request, EntityManagerInterface $em)
    {
        // do stuff and shit, like checking if the $token is good
        if ($user->getReinitialisationToken() != $token) {
            throw new \Exception("Le token fourni est invalide");
        }

        $form = $this->createForm(ReinitialisePasswordForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $user->setReinitialisationToken(null);
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                sprintf('Bonne nouvelle %s ! Ton mot de passe a été modifié avec succès ! En prime, on t\'a reconnecté, si c\'est pas beau tout ça.', $user->getPseudonym())
            );

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
        }

        return $this->render('AppBundle:front:passwordReinitilisation.html.twig', [
            'form' => $form->createView()
        ]);
    }
}