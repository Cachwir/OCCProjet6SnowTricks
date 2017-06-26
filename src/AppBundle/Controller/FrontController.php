<?php
/**
 * Created by PhpStorm.
 * User: cachwir
 * Date: 19/06/17
 * Time: 16:27
 */

namespace AppBundle\Controller;

use AppBundle\Entity\TrickPost;
use AppBundle\Form\Trick\ManageTrickForm;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FrontController extends Controller
{
    public function homeAction(Request $request)
    {
        return $this->render('AppBundle:front:home.html.twig', []);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function addTrickAction(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(ManageTrickForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickPost = $form->getData();
            $user = $this->getUser();
            $trickPost->setAuthor($user);

            $em->persist($trickPost);
            $em->flush();

            $this->addFlash(
                'success',
                sprintf('Félicitations %s ! Ton trick a été créé avec succès !', $this->getUser()->getPseudonym())
            );

            return $this->redirectToRoute("front_trick", ['id' => $trickPost->getId()]);
        }

        return $this->render('AppBundle:front:manageTrick.html.twig', [
            "form" => $form->createView(),
            "title" => "Nouveau trick"
        ]);
    }

    public function trickAction(TrickPost $trick, Request $request, EntityManagerInterface $em)
    {
        return $this->render('AppBundle:front:viewTrick.html.twig', [
            "trick" => $trick,
            "pathToTrickImages" => "/" . $this->getParameter("path_to_trick_images"),
        ]);
    }
}