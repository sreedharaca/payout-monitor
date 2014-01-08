<?php

namespace Katana\LogBundle\Service;

use Doctrine\ORM\EntityManager;
use Katana\LogBundle\Entity\CronLog;


class CronLogService {

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function save( $type, $message )
    {
        $em = $this->em;

        $Log = new CronLog();

        $Log->setType($type);
        $Log->setMessage($message);

        $em->persist($Log);

        $em->flush();
    }

}