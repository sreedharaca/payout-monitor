<?php

namespace Katana\JsonParserBundle\Parser;

use Symfony\Component\DependencyInjection\ContainerInterface;


class BaseJsonParser{

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

}
//TODO создать общий класс для hasoffers сеток