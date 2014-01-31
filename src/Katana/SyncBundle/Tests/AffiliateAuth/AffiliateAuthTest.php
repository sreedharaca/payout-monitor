<?php

namespace Katana\SyncBundle\Tests\AffiliateAuth;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class AffiliateAuthTest extends WebTestCase
{

    private $container;

    private $em;

    public function testClicksMobAuth()
    {
        $AuthManager = $this->container->get('AffiliateAuthManager');

        $Affiliate = $this->em->getRepository('KatanaAffiliateBundle:Affiliate')->findOneByName('Clicksmob');

        $Authenticator = $AuthManager->getAffiliateAuthenticator($Affiliate);

        $this->assertTrue($Authenticator->authenticate($Affiliate), 'Ошибка авторизации Clicksmob');
    }


    /***
     * Set Up container
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->container = static::$kernel->getContainer();

        $this->em = $this->container
            ->get('doctrine')
            ->getManager()
        ;
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}
