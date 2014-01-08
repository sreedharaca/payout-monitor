<?php

namespace Katana\LogBundle\Service;

use Doctrine\ORM\EntityManager;
use Katana\LogBundle\Entity\Log;


class LogService {

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function save( $action, $message, $offer = null )
    {
        $em = $this->em;

        $Log = new Log();

        $Log->setAction($action);
        $Log->setMessage($message);
        $Log->setOffer($offer);

        $em->persist($Log);

        $em->flush();
    }

}