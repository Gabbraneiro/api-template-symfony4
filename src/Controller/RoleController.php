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
 * @Rest\Route("/role")
 */
class RoleController extends AbstractFOSRestController
{
    /**
     * Da de alta un Rol.
     * @Rest\Post("")
     * @Rest\RequestParam(name="code", description="Código del rol", strict=true, nullable=false)
     * @Rest\RequestParam(name="name", description="Nombre del rol", strict=true, nullable=false)
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return void
     */
    public function createRole(ParamFetcherInterface $paramFetcher)
    {
        try {
            $rol = new Role(
                $paramFetcher->get('code'),
                $paramFetcher->get('name')
            );
            $this->getDoctrine()->getManager()->persist($rol);
            $this->getDoctrine()->getManager()->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new HttpException(400, "Ya existe un rol con el código: {$paramFetcher->get('codigo')}");
        }
        return $this->handleView($this->view($rol));
    }

    /**
     * Lista todos los Roles.
     * @Rest\Get("")
     * @return void
     */
    public function listRoles()
    {
        $roles = $this->getDoctrine()->getRepository(Role::class)->findAll();
        return $this->handleView($this->view($roles));
    }

    /**
     * Elimina el Rol indicado por parámetro.
     * @Rest\Delete("/{role}", requirements={"role"="\d+"})
     * @return void
     */
    public function deleteRole(Role $role)
    {
        $this->getDoctrine()->getManager()->remove($role);
        $this->getDoctrine()->getManager()->flush();
        $response = ['mensaje' => "Se eliminó el rol {$role->getName()}"];
        return $this->handleView($this->view($response));
    }

    /**
     * Asocia una Acción a un Rol.
     * @Rest\Post("/{role}/action/{action}", requirements={"role"="\d+", "action"="\d+"})
     * @return void
     */
      public function addActionToRole(Role $role, Action $action)
      {
          $role->addAction($action);
          $this->getDoctrine()->getManager()->flush();
          return $this->handleView($this->view($role));
      }
}
