<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

use App\Entity\Role;
use App\Entity\Action;

/**
 * @Rest\Route("/action")
 */
class ActionController extends AbstractFOSRestController
{
    /**
      * @Rest\Post("")
      * @Rest\RequestParam(name="code", description="Código de la acción", strict=true, nullable=false)
      * @Rest\RequestParam(name="name", description="Nombre de la acción", strict=true, nullable=false)
      *
      * @param ParamFetcherInterface $paramFetcher
      * @return void
      */
    public function createAction(ParamFetcherInterface $paramFetcher)
    {
        try {
            $action = new Action(
                $paramFetcher->get('code'),
                $paramFetcher->get('name')
            );
            $this->getDoctrine()->getManager()->persist($action);
            $this->getDoctrine()->getManager()->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new HttpException(400, "Ya existe una acción con el código: {$paramFetcher->get('code')}");
        }
        return $this->handleView($this->view($action));
    }

    /**
     * @Rest\Get("")
     * @return void
     */
    public function listActions()
    {
        $actions = $this->getDoctrine()->getRepository(Action::class)->findAll();
        return $this->handleView($this->view($actions));
    }

}
