<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testGetUser()
    {
        $task = new Task();
        $user = new User();

        $task->setUser($user);

        $this->assertSame('App\Entity\User', get_class($task->getUser()));
    }

    public function testGetUserAnonymous()
    {
        $task = new Task();

        $this->assertSame('Anonymous', $task->getUser());
    }

    public function testGetCreatedAt()
    {
        $task = new Task();

        $datetime = new \Datetime();

        $task->setCreatedAt($datetime);

        $this->assertSame($datetime, $task->getCreatedAt());
    }

    public function testGetTitle()
    {
        $task = new Task();

        $task->setTitle('Lorem Ipsum');

        $this->assertSame('Lorem Ipsum', $task->getTitle());
    }

    public function testGetContent()
    {
        $task = new Task();

        $task->setContent('Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        );

        $this->assertSame('Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
            $task->getContent()
        );
    }

    public function testIsDone()
    {
        $task = new Task();

        $task->toggle(1);

        $this->assertSame(1, $task->isDone());
    }
}