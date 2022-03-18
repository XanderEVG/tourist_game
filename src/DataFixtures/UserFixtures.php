<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Common\Auth\UserRoles;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
       $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
    }

    private function loadUsers(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setUsername("admin");
        $admin->setFio("admin");
        $admin->setPassword(
            $this->passwordHasher->hashPassword(
                $admin,
                'idy27ah_*nzn'
            )
        );
        $admin->addRole(UserRoles::ROLE_ADMIN);
        $admin->setEmail("xanderevg@gmail.com");
        $manager->persist($admin);
        $manager->flush();
    }
}
