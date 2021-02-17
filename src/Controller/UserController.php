<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

use App\Entity\User;
use App\Entity\Role;

class UserController extends AbstractFOSRestController
{
    private $passwordEncoder;
    private $jwtEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, JWTEncoderInterface $jwtEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtEncoder = $jwtEncoder;
    }

    /**
     * @Rest\Post("/login")
     * @Rest\RequestParam(name="username", description="Nombre de usuario", strict=true, nullable=false)
     * @Rest\RequestParam(name="password", description="Contraseña", strict=true, nullable=false)
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return void
     */
    public function loginAction(ParamFetcherInterface $paramFetcher)
    {
        $username = $paramFetcher->get('username');
        $password = $paramFetcher->get('password');
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByUsername($username);
        // Check User
        if (!$user) {
            throw new HttpException(404, "Usuario inexistente");
        }

        // Check Password
        if (!$this->passwordEncoder->isPasswordValid($user, $password)) {
            throw new HttpException(401, "Contraseña incorrecta");
        }

        // Create JWT token
        $dataUser = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'roles' => $user->getRoles()
        ];
        $token = ['token' => $this->jwtEncoder->encode($dataUser)];

        return $this->handleView($this->view($token));
    }

    /**
      * @Rest\Post("/user")
      * @Rest\RequestParam(name="username", description="Nombre de usuario", strict=true, nullable=false)
      * @Rest\RequestParam(name="password", description="Contraseña del usuario", strict=true, nullable=false)
      * @Rest\RequestParam(name="_password", description="Verificación de contraseña", strict=true, nullable=false)
      * @Rest\RequestParam(name="firstName", description="Nombre", strict=true, nullable=false)
      * @Rest\RequestParam(name="lastName", description="Apellido", strict=true, nullable=false)
      *
      * @param ParamFetcherInterface $paramFetcher
      * @return void
      */
    public function createUser(ParamFetcherInterface $paramFetcher)
    {
        if ($paramFetcher->get('password') != $paramFetcher->get('_password')) {
            throw new HttpException(400, "Las contraseñas no coinciden");
        }
        try {
            $user = new User(
                $paramFetcher->get('username'),
                $paramFetcher->get('firstName'),
                $paramFetcher->get('lastName')
            );
            $encodePassword = $this->passwordEncoder->encodePassword($user, $paramFetcher->get('password'));
            $user->setPassword($encodePassword);
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new HttpException(400, "Username existente");
        }
        return $this->handleView($this->view($user));
    }

    /**
      * @Rest\Put("/user/{user}", requirements={"user"="\d+"})
      * @Rest\RequestParam(name="username", description="Nombre de usuario", strict=true, nullable=true)
      * @Rest\RequestParam(name="firstName", description="Nombre", strict=true, nullable=true)
      * @Rest\RequestParam(name="lastName", description="Apellido", strict=true, nullable=true)
      *
      * @param ParamFetcherInterface $paramFetcher
      * @return void
      */
    public function updateUser(ParamFetcherInterface $paramFetcher, User $user)
    {
        try {
            if (!empty($paramFetcher->get('username'))) {
                $user->setUsername($paramFetcher->get('username'));
            }
            if (!empty($paramFetcher->get('firstName'))) {
                $user->setFirstName($paramFetcher->get('firstName'));
            }
            if (!empty($paramFetcher->get('lastName'))) {
                $user->setLastName($paramFetcher->get('lastName'));
            }
            
            $this->getDoctrine()->getManager()->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new HttpException(400, "El username {$paramFetcher->get('username')} ya existe, por favor elija otro.");
        }
        return $this->handleView($this->view($user));
    }

    /**
     * @Rest\Delete("/user/{user}", requirements={"user"="\d+"})
     * @return void
     */
    public function deleteUser(User $user)
    {
        $this->getDoctrine()->getManager()->remove($user);
        $this->getDoctrine()->getManager()->flush();
        $response = ['mensaje' => "Se eliminó el usuario {$user->getUsername()}"];
        return $this->handleView($this->view($response));
    }

    /**
      * @Rest\Put("/user/{user}/changePassword")
      * @Rest\RequestParam(name="password", description="Contraseña del usuario", strict=true, nullable=false)
      * @Rest\RequestParam(name="_password", description="Verificación de contraseña", strict=true, nullable=false)
      *
      * @param ParamFetcherInterface $paramFetcher
      * @return void
      */
    public function changePasswordUser(ParamFetcherInterface $paramFetcher, User $user)
    {
        if ($paramFetcher->get('password') != $paramFetcher->get('_password')) {
            throw new HttpException(400, "Las contraseñas no coinciden");
        }
        $encodePassword = $this->passwordEncoder->encodePassword($user, $paramFetcher->get('password'));
        $user->setPassword($encodePassword);
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();
        return $this->handleView($this->view($user));
    }

    /**
      * @Rest\Post("/user/{user}/role/{role}", requirements={"user"="\d+"})
      *
      * @param ParamFetcherInterface $paramFetcher
      * @return void
      */
    public function addRoleToUser(User $user, Role $role)
    {
        $user->addRole($role);
        $this->getDoctrine()->getManager()->flush();
        return $this->handleView($this->view($user));
    }
}
