<?php

namespace Katana\LogBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CronLogRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CronLogRepository extends EntityRepository
{
    public function findAll(){

        $qb = $this->createQueryBuilder('l')
            ->orderBy('l.created', 'DESC')
        ;

        $result = $qb->getQuery();

        $result->getMaxResults(100);

        return $result->execute();
    }
}