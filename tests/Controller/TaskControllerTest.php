<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TaskControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testTaskListPage()
    {
        $this->logInAsUser();
        $request = $this->client->request('GET', '/tasks');

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $request->filter('html:contains("Tâche n° 1")')->count());
    }

    public function testTaskListPageAsAnonymous()
    {
        $this->client->followRedirects();
        $request = $this->client->request('GET', '/tasks');

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame('Se connecter', $request->selectButton('Se connecter')->text());
    }

    public function testTaskCreatePage()
    {
        $this->logInAsUser();
        $request = $this->client->request('GET', '/tasks/create');

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame('Retour à la liste des tâches', $request->selectLink('Retour à la liste des tâches')->text());
    }

    public function testTaskCreatePageAsAnonymous()
    {
        $this->client->followRedirects();
        $request = $this->client->request('GET', '/tasks/create');

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame('Se connecter', $request->selectButton('Se connecter')->text());
    }

    public function testTaskCreation()
    {
        $this->logInAsUser();
        $this->client->followRedirects();
        $request = $this->client->request('GET', '/tasks/create');

        $form = $request->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Tâche n° 4';
        $form['task[content]'] = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        $request = $this->client->submit($form);

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(
            1,
            $request->filter('html:contains("La tâche a été bien été ajoutée.")')->count()
        );
    }

    public function testTaskEditPage()
    {
        $this->logInAsUser();
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'Tâche n° 2']);
        $request = $this->client->request('GET', '/tasks/' . $task->getId() . '/edit');

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $request->filter('html:contains("Modifier")')->count());
    }

    public function testTaskEditPageAsAnonymous()
    {
        $this->client->followRedirects();
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'Tâche n° 4']);
        $request = $this->client->request('GET', '/tasks/' . $task->getId() . '/edit');

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame('Se connecter', $request->selectButton('Se connecter')->text());
    }

    public function testTaskEditPageAsWrongUser()
    {
        $this->logInAsUser();
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'Tâche n° 5']);
        $this->client->request('GET', '/tasks/' . $task->getId() . '/edit');

        static::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testTaskEdition()
    {
        $this->logInAsUser();
        $this->client->followRedirects();
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'Tâche n° 2']);
        $request = $this->client->request('GET', '/tasks/' . $task->getId() . '/edit');

        $form = $request->selectButton('Modifier')->form();
        $form['task[title]'] = 'Tâche n° 2';
        $form['task[content]'] = 'Tâche modifiée.';
        $request = $this->client->submit($form);

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(
            1,
            $request->filter('html:contains("La tâche a bien été modifiée.")')->count()
        );
    }

    public function testTaskEditionAsAdmin()
    {
        $this->logInAsAdmin();
        $this->client->followRedirects();
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'Tâche n° 4']);
        $request = $this->client->request('GET', '/tasks/' . $task->getId() . '/edit');

        $form = $request->selectButton('Modifier')->form();
        $form['task[title]'] = 'Tâche n° 2';
        $form['task[content]'] = 'Tâche modifiée par admin.';
        $request = $this->client->submit($form);

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(
            1,
            $request->filter('html:contains("La tâche a bien été modifiée.")')->count()
        );
    }

    public function testTaskToggling()
    {
        $this->logInAsUser();
        $this->client->followRedirects();
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'Tâche n° 3']);
        $request = $this->client->request('GET', '/tasks/' . $task->getId() . '/toggle');

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(
            1,
            $request->filter('html:contains("La tâche ' . $task->getTitle() .' a bien été marquée comme faite.")')->count()
        );
    }

    public function testTaskTogglingAsWrongUser()
    {
        $this->logInAsUser();
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'Tâche n° 5']);
        $this->client->request('GET', '/tasks/' . $task->getId() . '/toggle');

        static::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testTaskTogglingAsAdmin()
    {
        $this->logInAsAdmin();
        $this->client->followRedirects();
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'Tâche n° 6']);
        $request = $this->client->request('GET', '/tasks/' . $task->getId() . '/toggle');

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(
            1,
            $request->filter('html:contains("La tâche ' . $task->getTitle() .' a bien été marquée comme faite.")')->count()
        );
    }

    public function testTaskDeletion()
    {
        $this->logInAsUser();
        $this->client->followRedirects();
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'Tâche n° 1']);
        $request = $this->client->request('GET', '/tasks/' . $task->getId() . '/delete');

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(
            1,
            $request->filter('html:contains("La tâche a bien été supprimée.")')->count()
        );
    }

    public function testTaskDeletionAsWrongUser()
    {
        $this->logInAsUser();
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'Tâche anonyme']);
        $this->client->request('GET', '/tasks/' . $task->getId() . '/delete');

        static::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testTaskDeletionAsAdmin()
    {
        $this->logInAsAdmin();
        $this->client->followRedirects();
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'Tâche anonyme']);
        $request = $this->client->request('GET', '/tasks/' . $task->getId() . '/delete');

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(
            1,
            $request->filter('html:contains("La tâche a bien été supprimée.")')->count()
        );
    }

    private function logInAsUser()
    {
        $session = static::$kernel->getContainer()->get('session');

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'brian']);

        $firewallName = 'main';
        $firewallContext = 'main';

        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    private function logInAsAdmin()
    {
        $session = static::$kernel->getContainer()->get('session');

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'admin']);

        $firewallName = 'main';
        $firewallContext = 'main';

        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}