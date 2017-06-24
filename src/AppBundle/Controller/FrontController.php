<?php
/**
 * Created by PhpStorm.
 * User: cachwir
 * Date: 19/06/17
 * Time: 16:27
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FrontController extends Controller
{
    public function homeAction(Request $Request)
    {
        return $this->render('AppBundle:front:home.html.twig', []);
    }
}