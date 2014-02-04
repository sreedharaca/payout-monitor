<?php

namespace Katana\StatusBundle\Controller;

use Katana\DictionaryBundle\Entity\Platform;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/status")
 */
class StatusController extends Controller
{
    /**
     * @Route("/", name="status")
     * @Template()
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();


        /** Offers */
        $offerTotal     = $em->getRepository('KatanaOfferBundle:Offer')->getTotalCount();

        $offerIos       = $em->getRepository('KatanaOfferBundle:Offer')->getCountByPlatform(Platform::IOS);

        $offerAndroid   = $em->getRepository('KatanaOfferBundle:Offer')->getCountByPlatform(Platform::ANDROID);

        $offerWeb       = $em->getRepository('KatanaOfferBundle:Offer')->getCountByPlatform(Platform::WEB);


        /** Apps */
        $appTotal   = $em->getRepository('KatanaOfferBundle:Offer')->getAppTotalCount();

        $appIos     = $em->getRepository('KatanaOfferBundle:Offer')->getAppCountByPlatform(Platform::IOS);

        $appAndroid = $em->getRepository('KatanaOfferBundle:Offer')->getAppCountByPlatform(Platform::ANDROID);

        $appWeb     = $em->getRepository('KatanaOfferBundle:Offer')->getAppCountByPlatform(Platform::WEB);


        /** Nets */
        $Affiliates = $em->getRepository('KatanaAffiliateBundle:Affiliate')->findAllAffiliates();

        $nets = array();

        foreach($Affiliates as $Affiliate)
        {
            $aff_stats = array();

            $aff_stats['name'] = $Affiliate->getName();

            //total
            $affTotal   = $em->getRepository('KatanaOfferBundle:Offer')->getAffiliateTotalCount($Affiliate);
            $aff_stats['total'] = $affTotal[0][1];

            //ios
            $affIos     = $em->getRepository('KatanaOfferBundle:Offer')->getCountByAffiliatePlatform($Affiliate, Platform::IOS);
            $aff_stats['ios'] = $affIos[0][1];

            //android
            $affAndroid = $em->getRepository('KatanaOfferBundle:Offer')->getCountByAffiliatePlatform($Affiliate, Platform::ANDROID);
            $aff_stats['android'] = $affAndroid[0][1];

            //web
            $affWeb     = $em->getRepository('KatanaOfferBundle:Offer')->getCountByAffiliatePlatform($Affiliate, Platform::WEB);
            $aff_stats['web'] = $affWeb[0][1];

            $nets[] = $aff_stats;
        }

        return array(
            'offers' => array(
                'total' => $offerTotal[0][1],
                'ios'   => $offerIos[0][1],
                'android'=>$offerAndroid[0][1],
                'web'   => $offerWeb[0][1]
            ),
            'apps'  => array(
                'total' => $appTotal[0][1],
                'ios'   => $appIos[0][1],
                'android'=>$appAndroid[0][1],
                'web'   => $appWeb[0][1]
            ),
            'nets'  => $nets
        );
    }
}
