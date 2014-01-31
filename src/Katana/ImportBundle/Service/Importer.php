<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 26.01.14
 * Time: 0:09
 */

namespace Katana\ImportBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class Importer {

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run()
    {
        echo "Importer run";
    }

} 