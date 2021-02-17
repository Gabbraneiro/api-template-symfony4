<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\User;
use App\Entity\Role;

class UserFixture extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $gab = new User(
            'gab',
            'Gabriel',
            'Brañeiro'
        );
        $role = new Role(
            'ADMIN',
            'Administrador'
        );
        $manager->persist($role);
        $gab->setPassword($this->passwordEncoder->encodePassword($gab, 'gab_password'));
        $gab->addRole($role);
        $manager->persist($gab);
        $manager->flush();
    }
}
