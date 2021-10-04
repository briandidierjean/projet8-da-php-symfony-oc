<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('brian');
        $password = $this->passwordEncoder->encodePassword($user,'password');
        $user->setPassword($password);
        $user->setEmail('brian@mail.com');
        $manager->persist($user);

        $task = new Task();
        $task->setUser($user);
        $task->setTitle('Tâche n° 1');
        $task->setContent('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');
        $manager->persist($task);

        $task = new Task();
        $task->setUser($user);
        $task->setTitle('Tâche n° 2');
        $task->setContent('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');
        $manager->persist($task);

        $task = new Task();
        $task->setUser($user);
        $task->setTitle('Tâche n° 3');
        $task->setContent('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');
        $manager->persist($task);

        $admin = new User();
        $admin->setUsername('admin');
        $password = $this->passwordEncoder->encodePassword($admin,'password');
        $admin->setPassword($password);
        $admin->setEmail('admin@mail.com');
        $admin->addRole('ROLE_ADMIN');
        $manager->persist($admin);

        $user = new User();
        $user->setUsername('nicolas');
        $password = $this->passwordEncoder->encodePassword($user,'password');
        $user->setPassword($password);
        $user->setEmail('nicolas@mail.com');
        $manager->persist($user);

        $task = new Task();
        $task->setUser($user);
        $task->setTitle('Tâche n° 4');
        $task->setContent('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');
        $manager->persist($task);

        $task = new Task();
        $task->setUser($user);
        $task->setTitle('Tâche n° 5');
        $task->setContent('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');
        $manager->persist($task);

        $task = new Task();
        $task->setUser($user);
        $task->setTitle('Tâche n° 6');
        $task->setContent('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');
        $manager->persist($task);

        $task = new Task();
        $task->setTitle('Tâche anonyme');
        $task->setContent('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');
        $manager->persist($task);

        $manager->flush();
    }
}