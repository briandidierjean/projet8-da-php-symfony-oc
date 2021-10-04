<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserControllerTest extends WebTestCase
{
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testUserListPage()
    {
        $this->logInAsAdmin();
        $request = $this->client->request('GET', '/users');

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $request->filter('html:contains("Liste des utilisateurs")')->count());
    }

    public function testUserListPageAsUser()
    {
        $this->logInAsUser();
        $this->client->request('GET', '/users');

        static::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testUserListPageAsAnonymous()
    {
        $this->client->followRedirects();
        $request = $this->client->request('GET', '/users');

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame('Se connecter', $request->selectButton('Se connecter')->text());
    }

    public function testUserCreatePage()
    {
        $request = $this->client->request('GET', '/users/create');

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $request->filter('html:contains("Créer un utilisateur")')->count());
    }

    public function testUserCreatePageAsAdmin()
    {
        $this->logInAsAdmin();
        $request = $this->client->request('GET', '/users/create');

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $request->filter('html:contains("Créer un utilisateur")')->count());
        static::assertSame(1, $request->filter('input#user_isAdmin')->count());
    }

    public function testUserCreation()
    {
        $this->client->followRedirects();
        $request = $this->client->request('GET', '/users/create');

        $form = $request->selectButton('Ajouter')->form();
        $form['user[username]'] = 'martin';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'martin@mail.com';
        $request = $this->client->submit($form);

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(
            1,
            $request->filter('html:contains("L\'utilisateur a bien été ajouté.")')->count()
        );
    }

    public function testUserCreationAsAdmin()
    {
        $this->logInAsAdmin();
        $this->client->followRedirects();
        $request = $this->client->request('GET', '/users/create');

        $form = $request->selectButton('Ajouter')->form();
        $form['user[username]'] = 'admin2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'admin2@mail.com';
        $form['user[isAdmin]'] = 1;
        $request = $this->client->submit($form);

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(
            1,
            $request->filter('html:contains("L\'utilisateur a bien été ajouté.")')->count()
        );
    }

    public function testUserEditPage()
    {
        $this->logInAsAdmin();
        $user = self::$container->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'nicolas']);
        $request = $this->client->request('GET', '/users/'. $user->getId() . '/edit');

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $request->filter('html:contains("Modifier ' . $user->getUsername() . '")')->count());
    }

    public function testUserEditPageAsUser()
    {
        $this->logInAsUser();
        $user = self::$container->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'nicolas']);
        $this->client->request('GET', '/users/'. $user->getId() . '/edit');

        static::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testUserEditPageAsAnonymous()
    {
        $user = self::$container->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'nicolas']);
        $this->client->followRedirects();
        $request = $this->client->request('GET', '/users/'. $user->getId() . '/edit');

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame('Se connecter', $request->selectButton('Se connecter')->text());
    }

    public function testUserEdition()
    {
        $this->logInAsAdmin();
        $user = self::$container->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'nicolas']);
        $this->client->followRedirects();
        $request = $this->client->request('GET', '/users/'. $user->getId() . '/edit');

        $form = $request->selectButton('Modifier')->form();
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'nicolas02@mail.com';
        $form['user[isAdmin]'] = 1;
        $request = $this->client->submit($form);

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame(
            1,
            $request->filter('html:contains("L\'utilisateur a bien été modifié")')->count()
        );
    }

    private function logInAsUser()
    {
        $session = self::$container->get('session');

        $user = self::$container->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'brian']);

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
        $session = self::$container->get('session');

        $user = self::$container->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'admin']);

        $firewallName = 'main';
        $firewallContext = 'main';

        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}