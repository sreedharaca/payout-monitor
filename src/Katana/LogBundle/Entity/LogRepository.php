<?php

namespace Katana\LogBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * LogRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LogRepository extends EntityRepository
{

    public function findAllLogs(){

        $qb = $this->createQueryBuilder('l')
            ->orderBy('l.created', 'DESC')
        ;

        $result = $qb->getQuery();

        $result->getMaxResults(300);


        return $result->execute();
    }
}
