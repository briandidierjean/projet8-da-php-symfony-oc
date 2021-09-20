<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetUsername()
    {
        $user = new User();

        $user->setUsername('johnsmith');

        $this->assertSame('johnsmith', $user->getUsername());
    }

    public function testGetSalt()
    {
        $user = new User();

        $this->assertSame(null, $user->getSalt());
    }

    public function testGetPassword()
    {
        $user = new User();

        $user->setPassword('Password0!');

        $this->assertSame('Password0!', $user->getPassword());
    }

    public function testGetEmail()
    {
        $user = new User();

        $user->setEmail('johnsmith@mail.com');

        $this->assertSame('johnsmith@mail.com', $user->getEmail());
    }

    public function testGetRoles()
    {
        $user = new User();

        $user->addRole('ROLE_ADMIN');

        $this->assertSame(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());
    }

    public function testGetTasks()
    {
        $user = new User();

        $task = new Task();

        $user->addTask($task);

        $this->assertSame('App\Entity\Task', get_class($user->getTasks()->get(0)));
    }
}