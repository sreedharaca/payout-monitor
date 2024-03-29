<?php

namespace Katana\DictionaryBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CountryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CountryRepository extends EntityRepository
{
    public function findByOffer($Offer)
    {
        $qb = $this->createQueryBuilder('country')
            ->select('country')
            ->join('country.offers', 'offer')
            ->where('offer = :offer')
            ->setParameter('offer', $Offer);

        return $qb->getQuery()->execute();
    }
}
