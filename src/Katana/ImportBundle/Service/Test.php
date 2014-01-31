<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 26.01.14
 * Time: 0:09
 */

namespace Katana\ImportBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Katana\AffiliateBundle\Entity\RawData;


class Test {

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run()
    {
        echo "Test run";

        $url = 'http://afftrack.cpaway.com/api/?key=b3ee8579dc25f07167247b2f4359da60&action=offers&format=json&limit=1';

        $buzz = $this->container->get('buzz.browser');

        $data = $buzz->get($url);

        echo 'got ' . round(strlen($data) / 1024) . "kb\n";

        $rd = new RawData();
        $rd->setData($data->getContent());

        $em = $this->container->get('doctrine')->getManager();
        $Af = $em->getRepository('KatanaAffiliateBundle:Affiliate')->find(1);

        $rd->setAffiliate($Af);


        $em->persist($rd);

        $em->flush();
    }

} 