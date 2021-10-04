<?php

namespace App\DataFixtures;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $existingUsers = $manager
            ->getRepository(User::class)
            ->findBy(['email' => ['test_readonly@gmail.com','test_full@gmail.com']]);
        $existingUsersAsc = array();
        array_map(
            function ($el) use (&$existingUsersAsc) {
                $existingUsersAsc[$el->getEmail()] = $el;
            },
            $existingUsers
        );
        unset($existingUsers);

        if (!isset($existingUsersAsc['test_readonly@gmail.com'])) {
            $user = new User();
            $user->setEmail('test_readonly@gmail.com');
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                'testreadpassword'
            ));
            $manager->persist($user);
        }

        if (!isset($existingUsersAsc['test_full@gmail.com'])) {
            $user = new User();
            $user->setEmail('test_full@gmail.com');
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                'testfullpassword'
            ));
            $user->setRoles(array('ROLE_FULL'));
            $manager->persist($user);
        }
        $manager->flush();
    }
}
