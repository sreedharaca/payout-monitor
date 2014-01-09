<?php

namespace Katana\OfferBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Katana\DictionaryBundle\Entity\Device;
use Katana\DictionaryBundle\Entity\Platform;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Katana\OfferBundle\Entity\App;

/**
 * OfferRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OfferRepository extends EntityRepository
{
    public function findAllOffers($sort = 'created', $order = 'desc')
    {
        $qb = $this->createQueryBuilder('offer')
            ->select('offer, d, c, app')
            ->leftJoin('offer.app', 'app')
            ->leftJoin('offer.devices', 'd')
            ->leftJoin('offer.countries', 'c')
//            ->leftJoin('offer.affiliate', 'a') //TODO json данные аффилиата хранить в отдельной таблице чтобы без проблем делать джоин аффилиата
//            ->orderBy('offer.' . $sort, $order);
            ->where('offer.deleted != 1')
        ;

        return $qb->getQuery()->execute();
    }

    public function findAllMobile(){

        $qb = $this->createQueryBuilder('offer')
            ->select('offer, d, c, app')
            ->join('offer.platform', 'p')
            ->leftJoin('offer.app', 'app')
            ->leftJoin('offer.devices', 'd')
            ->leftJoin('offer.countries', 'c')
            ->where('offer.deleted != 1')
            ->andWhere('p.name IN (:platforms)')
            ->setParameter(':platforms', array(Platform::IOS, Platform::ANDROID))
        ;

        return $qb->getQuery()->execute();

    }

    public function findAllOffersWithApps($sort = 'created', $order = 'desc')
    {
        $qb = $this->createQueryBuilder('offer')
            ->select('offer, d, c, a, app')
            ->join('offer.app', 'app')
            ->leftJoin('offer.devices', 'd')
            ->leftJoin('offer.countries', 'c')
            ->leftJoin('offer.affiliate', 'a')
            ->orderBy('offer.' . $sort, $order);
        ;

        return $qb->getQuery()->execute();
    }

    public function getByFiltersData($data)
    {
        $qb = $this->createQueryBuilder('offer')
            ->select('offer, d, c, a, app')
            ->leftJoin('offer.app', 'app')
            ->leftJoin('offer.devices', 'd')
            ->leftJoin('offer.countries', 'c')
            ->leftJoin('offer.affiliate', 'a')
        ;

        /** Affiliate */
        if(!empty($data['affiliate']) && is_object($data['affiliate']))
        {
            $qb
//                ->join('offer.affiliate', 'affiliate')
                ->andWhere('a = :affiliate')
                ->setParameter('affiliate', $data['affiliate']);
        }

        /** Страна */
        if( !empty($data['country']) && ($data['country'] instanceof ArrayCollection) && count($data['country']->toArray()) )
        {
            $country_ids = array();
            foreach($data['country'] as $country){
                $country_ids[] = $country->getId();
            }

            $qb
//                ->join('offer.countries', 'c')
                ->andWhere('c.id IN (:country_ids)')
                ->setParameter('country_ids', $country_ids);
        }

        /** Платформа */
        if( !empty($data['platform']) && ($data['platform'] instanceof ArrayCollection) && count($data['platform']->toArray()) )
        {
            $platform_ids = array();
            foreach($data['platform'] as $platform){
                $platform_ids[] = $platform->getId();
            }
            $qb
                ->leftJoin('offer.platform', 'p')
                ->andWhere('p.id IN (:platform_ids)')
                ->setParameter('platform_ids', $platform_ids );
        }

        /** Девайс */
        if( !empty($data['device']) && ($data['device'] instanceof ArrayCollection) && count($data['device']->toArray()) )
        {
            $device_ids = array();
            foreach($data['device'] as $device){
                $device_ids[] = $device->getId();
            }
            $qb
//                ->join('offer.devices', 'd')
                ->andWhere('d.id IN (:device_ids)')
                ->setParameter('device_ids', $device_ids );
        }

        /** Incent */
        if( !empty($data['incentive']))
        {
            $qb->andWhere('offer.incentive = :incentive')
                ->setParameter('incentive', $data['incentive']);
        }

        /** New */
        if( !empty($data['new']))
        {
            $qb->andWhere('offer.new = :new')
                ->setParameter('new', $data['new']);
        }

        /** Search */
        if( !empty($data['search']))
        {
            $qb->andWhere('lower(offer.name) LIKE :search OR lower(app.name) LIKE :search')
                ->setParameter('search', strtolower('%' . $data['search'] . '%'));
        }

//        echo $qb->getQuery()->getSQL();

        return $qb->getQuery()->execute();
    }

    public function findIOS()
    {
        $Platform = $this->getRepository("KatanaDoictionaryBundle:Platform")->findOneByName(Platform::IOS);

        if(empty($Platform)){
            throw new \Exception('Не найдена платформа:' . Platform::IOS);
        }

        $qb = $this->createQueryBuilder('o')
            ->select('o')
            ->join('o.platform', 'p')
            ->where('p = :platform')
            ->setParameter(':platform', $Platform);

        return $qb->getQuery()->execute();
    }

    public function getNoneApps(){
        $qb = $this->createQueryBuilder('offer')
            ->select('offer, d, c')
            ->leftJoin('offer.devices', 'd')
            ->leftJoin('offer.countries', 'c')
            ->where("offer.app is NULL")
        ;

        return $qb->getQuery()->execute();
    }

    public function batchDeactivate($ids, $Affiliate){

        if( ! count($ids) ){
            return false;
        }

        $this->createQueryBuilder('o')
            ->update('Katana\OfferBundle\Entity\Offer', 'o') //$this->getClassName()
            ->set('o.active', 0)
            ->set('o.deleted', 1)
//            ->set('h.updatedBy', $this->user->getId())
            ->where('o.external_id IN (:ids)')
            ->andWhere('o.affiliate = (:affiliate)')
            ->setParameter('ids', $ids)
            ->setParameter('affiliate', $Affiliate)
            ->getQuery()->execute()
        ;

//        $qb = $this->createQueryBuilder('KatanaOfferBundle:Offer');
//        $qb->update('KatanaOfferBundle:Offer o')
//            ->set('o.active', 0)
//            ->where('o.id IN (:ids)')
//            ->setParameter('ids', $ids)
//            ;
//        return $qb->getQuery()->execute();
    }

    /***
     * Найти конкурентов для данного оффера
     */
    public function findCompetitors($id){

        if(!$id){
            throw new NotFoundHttpException("Не передан id оффера.");
        }

        $offer = $this->find($id);

        if(empty($offer)){
            throw new NotFoundHttpException("Оффер не найден.");
        }

        $app = $offer->getApp();

        //нет конкурентов
        if(empty($app)){
            return null;
        }

        $qb = $this->createQueryBuilder('offer')
            ->select('offer, d, c, a, app')
            ->join('offer.app', 'app')
            ->leftJoin('offer.devices', 'd')
            ->leftJoin('offer.countries', 'c')
            ->leftJoin('offer.affiliate', 'a')
            ->where('offer.id != :id')
            ->setParameter(':id', $id)
            ->andWhere('app.id = :app_id')
            ->setParameter('app_id', $app->getId())
        ;

        return $qb->getQuery()->execute();
    }

    public function findBestByApp(App $app){

        $qb = $this->createQueryBuilder('offer')
            ->select('offer, d, c, a, app')
            ->join('offer.app', 'app')
            ->leftJoin('offer.devices', 'd')
            ->leftJoin('offer.countries', 'c')
            ->leftJoin('offer.affiliate', 'a')
            ->where('offer.app = :app')
            ->setParameter('app', $app)
            ->orderBy('offer.payout', 'DESC')
        ;

        $query = $qb->getQuery();

        $query->setMaxResults(1);

        return $query->execute();
    }
}