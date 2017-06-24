<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\User\ForgotPasswordForm;
use AppBundle\Form\User\LoginForm;
use AppBundle\Form\User\ReinitialisePasswordForm;
use AppBundle\Form\User\UserAvatarForm;
use AppBundle\Form\User\UserEmailForm;
use AppBundle\Form\User\UserPasswordForm;
use AppBundle\Form\User\UserPseudonymForm;
use AppBundle\Form\User\UserRegistrationForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
                sprintf('Ton mot de passe a été modifié avec succès ! Tu es maintenant connecté(e).', $user->getPseudonym())
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

    public function parametersAction(Request $request, EntityManagerInterface $em)
    {
        $sessionUser = $this->getUser();
        $user = $em->getRepository('AppBundle:User')->find($sessionUser->getId());

        $forms = [
            "emailForm" => $this->createForm(UserEmailForm::class, $user),
            "passwordForm" => $this->createForm(UserPasswordForm::class, $user),
            "pseudonymForm" => $this->createForm(UserPseudonymForm::class, $user),
            "avatarForm" => $this->createForm(UserAvatarForm::class, $user),
        ];

        $template_params = [
            "user" => $user,
            "pathToAvatars" => "/" . $this->getParameter("path_to_avatars"),
        ];

        foreach ($forms as $name => $form) {
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $user = $form->getData();

                    /**
                     * @var User $user
                     */
                    $user->setUpdatedAtNow();

                    $em->persist($user);
                    $em->flush();

                    $this->addFlash(
                        'success',
                        sprintf('Informations modifiées avec succès !')
                    );

                    return $this->redirectToRoute("security_parameters");
                }

                $em->refresh($user);
            }

            $template_params[$name] = $form->createView();
        }

        return $this->render('AppBundle:front:userParameters.html.twig', $template_params);
    }
}