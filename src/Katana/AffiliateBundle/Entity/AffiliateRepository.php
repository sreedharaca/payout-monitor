<?php

namespace Katana\AffiliateBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * AffiliateRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AffiliateRepository extends EntityRepository
{
    public function findAllAffiliates()
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.active = 1')
            ->orderBy('a.id', 'asc')
        ;

        return $qb->getQuery()->execute();
    }
}
