<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testHomepage()
    {
        $this->logIn();
        $request = $this->client->request('GET', '/');

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame('Se déconnecter', $request->selectLink('Se déconnecter')->text());
    }

    public function testHomepageAsAnonymous()
    {
        $this->client->followRedirects();
        $request = $this->client->request('GET', '/');

        static::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertSame('Se connecter', $request->selectButton('Se connecter')->text());
    }

    private function logIn()
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
}