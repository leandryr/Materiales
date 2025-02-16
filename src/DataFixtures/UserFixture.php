<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('alpatino@alpatino.com');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            '123'
        ));
        $user->setPlainPassword('123');
        $user->setNombre('alberto patino');
        $user->setActivo(true);
        $user->setRoles(['ROLE_ADMINISTRADOR']);
        $manager->persist($user);

        $manager->flush();
    }
}
