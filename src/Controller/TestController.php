<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class TestController extends \FOS\RestBundle\Controller\AbstractFOSRestController
{
    // /**
    //  * @Route("/test", name="ss")
    //  */
    // public function test()
    // {   
    //     return $this->handleView($this->view($this->getUser()));
    // }
}