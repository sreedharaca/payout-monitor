<?php

namespace Katana\JsonParserBundle\Parser;

use Symfony\Component\DependencyInjection\ContainerInterface;


class BaseParser{

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

}
//TODO создать общий класс для hasoffers сеток