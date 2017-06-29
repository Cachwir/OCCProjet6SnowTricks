<?php
/**
 * Created by PhpStorm.
 * User: cachwir
 * Date: 19/06/17
 * Time: 16:27
 */

namespace AppBundle\Controller;

use AppBundle\Entity\TrickPost;
use AppBundle\Entity\TrickPostComment;
use AppBundle\Form\Trick\ManageTrickForm;
use AppBundle\Form\Trick\NewCommentForm;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FrontController extends Controller
{
    public function homeAction(Request $request, EntityManagerInterface $em)
    {
        $trickPosts = $em->getRepository("AppBundle:TrickPost")->findAll();
        $trickTags = $em->getRepository("AppBundle:TrickTag")->findAll();

        return $this->render('AppBundle:front:home.html.twig', [
            "trickPosts" => $trickPosts,
            "trickTags" => $trickTags,
            "pathToTrickImages" => "/" . $this->getParameter("path_to_trick_images"),
        ]);
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
            $trickPost->setCreationDate(time());

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

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function editTrickAction(TrickPost $trickPost, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(ManageTrickForm::class, $trickPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickPost = $form->getData();
            $user = $this->getUser();
            $trickPost->setLastContributor($user);
            $trickPost->setUpdatedAt(time());

            $em->persist($trickPost);
            $em->flush();

            $this->addFlash(
                'success',
                sprintf('Félicitations %s ! Ce trick a été édité avec succès !', $this->getUser()->getPseudonym())
            );

            return $this->redirectToRoute("front_trick", ['id' => $trickPost->getId()]);
        }

        return $this->render('AppBundle:front:manageTrick.html.twig', [
            "form" => $form->createView(),
            "title" => "Editer un trick",
            "pathToTrickImages" => "/" . $this->getParameter("path_to_trick_images"),
        ]);
    }

    public function trickAction(TrickPost $trickPost, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(NewCommentForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->isGranted("ROLE_USER")) {
            /**
             * @var TrickPostComment $trickPostComment
             */
            $trickPostComment = $form->getData();
            $user = $this->getUser();
            $trickPostComment->setTrickPost($trickPost);
            $trickPostComment->setAuthor($user);
            $trickPostComment->setPublicationDate(time());

            $em->persist($trickPostComment);
            $em->flush();

            $this->addFlash(
                'success',
                sprintf('Félicitations %s ! Ton commentaire a été ajouté avec succès !', $this->getUser()->getPseudonym())
            );

            return $this->redirectToRoute("front_trick", ['id' => $trickPost->getId()]);
        }

        $trickpostComments = $em->getRepository("AppBundle:TrickPostComment")->findBy(["trickPost" => $trickPost]);

        $template_params = [
            "trick" => $trickPost,
            'trickComments' => $trickpostComments,
            "pathToTrickImages" => "/" . $this->getParameter("path_to_trick_images"),
            "pathToAvatars" => "/" . $this->getParameter("path_to_avatars"),
        ];

        if ($this->isGranted("ROLE_USER")) {
            $template_params["form"] = $form->createView();
        }

        return $this->render('AppBundle:front:viewTrick.html.twig', $template_params);
    }
}