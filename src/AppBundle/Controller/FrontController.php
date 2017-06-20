<?php
/**
 * Created by PhpStorm.
 * User: cachwir
 * Date: 19/06/17
 * Time: 16:27
 */

namespace AppBundle\Controller;

use AppBundle\Form\LoginForm;
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
//        $form = $this->createForm(LoginForm::class);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $data = $form->getData();
//
//            $this->addFlash(
//                'success',
//                sprintf('Genus created by you: %s!', $this->getUser()->getEmail())
//            );
//
//
//        }

        return $this->render('AppBundle:front:home.html.twig', []);
    }
}