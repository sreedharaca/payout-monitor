<?php

namespace Katana\OfferBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Katana\OfferBundle\Form\OfferFilterType;
use Katana\DictionaryBundle\Entity\Platform;


/**
 * Offer Controller.
 *
 * @Route("/offer")
 */
class OfferController extends Controller
{
    /**
     *
     * @Route("/1", name="offers_temp")
     * @Method("GET")
     * @Template("KatanaOfferBundle:Offer:temp.html.twig")
     */
    public function tempAction()
    {

        return array(
            'form' => $this->createForm(new OfferFilterType())->createView(),
        );
    }


    /**
     *
     * @Route("/", name="offers")
     * @Method("GET")
     * @Template("KatanaOfferBundle:Offer:index.html.twig")
     */
    public function indexAction()
    {
        $all_offers = $this->getDoctrine()->getRepository("KatanaOfferBundle:Offer")->findAllOffers();
//        $all_offers = $this->getDoctrine()->getRepository("KatanaOfferBundle:Offer")->findAll();

        list($apps, $nonGroupedOffers) = $this->groupOffers($all_offers);

        /*** FIND BEST PAYOUT  */
        foreach($apps as $id => $app){
            $sortedOffers = $this->sortOffersByPayoutAndGeo($app['offers']);

            $bestOffer = array_pop($sortedOffers);
            $apps[$id]['bestOffer'] = $bestOffer;
            $apps[$id]['offers'] = $sortedOffers;
        }

        /*** SORT APPS */
        $apps = $this->sortAppsByName($apps);


        return array(
            'form' => $this->createForm(new OfferFilterType())->createView(),
            'nonGroupedOffers' => $nonGroupedOffers,
            'apps' => $apps
        );
    }


    /**
     *
     * @Route("/side", name="sidepanel")
     * @Method("GET")
     * @Template("KatanaOfferBundle:Offer:side.html.twig")
     */
    public function sidepanelAction()
    {
        $all_offers = $this->getDoctrine()->getRepository("KatanaOfferBundle:Offer")->findAllMobile();
//        $all_offers = $this->getDoctrine()->getRepository("KatanaOfferBundle:Offer")->findAll();

        list($apps, $nonGroupedOffers) = $this->groupOffers($all_offers);

        /*** FIND BEST PAYOUT  */
        foreach($apps as $id => $app){
            $sortedOffers = $app['offers'] ;//$this->sortOffersByPayoutAndGeo($app['offers']);

            $bestOffer = array_pop($sortedOffers);
            $apps[$id]['bestOffer'] = $bestOffer;
            $apps[$id]['offers'] = $sortedOffers;
        }


        $iosApps = array();
        $androidApps = array();

        /*** Разделить по платформам */
        foreach($apps as $id => $app){

            if($app['bestOffer']->getPlatform()->getName() == Platform::IOS){
                $iosApps[$id] = $app;
            }
            elseif($app['bestOffer']->getPlatform()->getName() == Platform::ANDROID){
                $androidApps[$id] = $app;
            }
        }

        /*** SORT APPS */
        $iosApps = $this->sortAppsByName($iosApps);
        $androidApps = $this->sortAppsByName($androidApps);


        /***
         * Буквенный индекс IOS
         */
        $iosLetters = array();

        foreach($iosApps as $app){
            $offer = $app['bestOffer'];

            $name = $offer->getApp()->getName()?:$offer->getName();

            $letter = strtoupper(substr($name,0,1));

            if(! in_array($letter, $iosLetters)){
                $iosLetters[] = $letter;
            }
        }


        /***
         * Буквенный индекс ANDROID
         */
        $androidLetters = array();

        foreach($androidApps as $app){
            $offer = $app['bestOffer'];

            $name = $offer->getApp()->getName()?:$offer->getName();

            $letter = strtoupper(substr($name,0,1));

            if(! in_array($letter, $androidLetters)){
                $androidLetters[] = $letter;
            }
        }


        return array(
            'form' => $this->createForm(new OfferFilterType())->createView(),
            'nonGroupedOffers' => $nonGroupedOffers,
            'iosApps' => $iosApps,
            'androidApps' => $androidApps,
            'iosLetters' => $iosLetters,
            'androidLetters' => $androidLetters
        );
    }

    /**
     *
     * @Route("/filter", name="filter_offers")
     * @Method("POST")
     * @Template("KatanaOfferBundle:Offer:index.html.twig")
     */
    public function filterOffersAction(Request $request)
    {
        $form = $this->createForm(new OfferFilterType());
        $form->submit($request);

        if($form->isValid())
        {
            $data = $form->getData();

            $all_offers = $this->getDoctrine()->getRepository("KatanaOfferBundle:Offer")->getByFiltersData($data);

            list($apps, $nonGroupedOffers) = $this->groupOffers($all_offers);

            /*** FIND BEST PAYOUT  */
            foreach($apps as $id => $app){
                $sortedOffers = $this->sortOffersByPayoutAndGeo($app['offers']);

                $bestOffer = array_pop($sortedOffers);
                $apps[$id]['bestOffer'] = $bestOffer;
                $apps[$id]['offers'] = $sortedOffers;
            }

            /*** SORT APPS */
            $apps = $this->sortAppsByName($apps);
        }


        return array(
            'form' => $form->createView(),
            'nonGroupedOffers' => $nonGroupedOffers,
            'apps' => $apps
        );
    }

    private function groupOffers($all_offers)
    {
        /** Сгруппированные офферы */
        /***
         *  app_id:
         *      'app': App
         *      'offers': [Offers]
         */
        $apps = array();
        /** Не сгруппированные офферы */
        $nonGroupedOffers = array();

        foreach($all_offers as $offer)
        {
            $App = $offer->getApp();

            if( !empty( $App ) ){

                $app_id = $offer->getApp()->getId();

                if(!isset($apps[$app_id])) {
                    $apps[$app_id] = array(
                        'app'   => $offer->getApp(),
                        'offers' => array()
                    );
                }

                $apps[$app_id]['offers'][] = $offer;
            }
            else{
                $nonGroupedOffers[] = $offer;
            }
        }

        return array(
            $apps,
            $nonGroupedOffers
        );
    }

    /***
     * @param array
     * $returns array
     */
    private function sortOffersByPayoutAndGeo($Offers)
    {
        usort($Offers, function($Offer1, $Offer2){

            /** сортируем по возрастанию Payout */
            if( $Offer1->getPayout() == $Offer2->getPayout() ){
                /** Если ставка одинаковая то по широте ГЕО */
                if( count($Offer1->getCountries()) == count($Offer2->getCountries()) ){
                    return 0;
                }

                return (count($Offer1->getCountries()) < count($Offer2->getCountries())) ? -1 : 1;
            }

            return ($Offer1->getPayout() < $Offer2->getPayout()) ? -1 : 1;
        });


        return $Offers;
    }

    private function sortAppsByName($apps)
    {
        usort($apps, function($app1, $app2){

            $name1 = $app1['app']->getName();
            if(!strlen($app1['app']->getName())){
                $name1 = $app1['bestOffer']->getName();
            }

            $name2 = $app2['app']->getName();
            if(!strlen($app2['app']->getName())){
                $name2 = $app2['bestOffer']->getName();
            }

            return strcasecmp($name1, $name2);
        });

        return $apps;
    }

    /**
     *
     * @Route("/noneapp", name="none_app_offers")
     * @Method("GET")
     * @Template("KatanaOfferBundle:Offer:noneapp.html.twig")
     */
    public function noneAppAction()
    {
        $offers = $this->getDoctrine()->getRepository("KatanaOfferBundle:Offer")->getNoneApps();


        return array(
            'offers' => $offers
        );

    }

    /**
     *
     * @Route("/apps", name="apps")
     * @Method("GET")
     * @Template("KatanaOfferBundle:Offer:apps.html.twig")
     */
    public function appsAction()
    {
        $apps = $this->getDoctrine()->getRepository("KatanaOfferBundle:App")->findAll();

        return array(
            'apps' => $apps
        );
    }


    /**
     * @Route("/delete", name="offer_delete")
     * @Method("POST")
     *
     * , requirements={"id" = "\d+"}
     */
    public function deleteAction(Request $request)
    {
        $id = (int) $request->request->get('id');

        if (!$id) {
            throw $this->createNotFoundException('Не передан параметр id.');
        }

        $em = $this->getDoctrine()->getManager();

        $offer = $em->getRepository('KatanaOfferBundle:Offer')->find($id);

        if (!$offer || $offer->getDeleted()) {
            throw $this->createNotFoundException('Оффер не найден.');
        }

        $offer->setDeleted(true);
        $offer->setActive(false);

        $em->flush();

        return new JsonResponse(array('status' => 'ok'));
    }


    /**
     * @Route("/ajax_filter", name="offer_ajax_filter")
     * @Method("POST")
     */
    public function ajaxFilterAction(){

        $offerItem = array(
            'icon' =>  'http://a1008.phobos.apple.com/us/r30/Purple4/v4/6c/7d/9e/6c7d9e65-d9ff-2b1b-94f3-1434f0794423/Icon.png',
            'partner' =>  'Satana',
            'name' =>  'Spartan Hyis',
            'payout' =>  1.55,
            'device' =>  'iPhone',
            'countries' =>  array('US'),
            'is_incentive' =>  true,
            'is_new' =>  true
        );

        $iosOffers = array(
            array(
                'letter' => 'A',
                'offers' => array(
                    array(
                        'id' => 1,
                        'icon' =>  'http://a1008.phobos.apple.com/us/r30/Purple4/v4/6c/7d/9e/6c7d9e65-d9ff-2b1b-94f3-1434f0794423/Icon.png',
                        'partner' =>  'Katana',
                        'name' =>  'Spartan Wars1',
                        'payout' =>  1.55,
                        'device' =>  'iPhone',
                        'countries' =>  array('US'),
                        'is_incentive' =>  true,
                        'is_new' =>  true,
                        'relative_offers' => array($offerItem, $offerItem, $offerItem)
                    ),
                    array(
                        'id' => 2,
                        'icon' =>  'http://a1008.phobos.apple.com/us/r30/Purple4/v4/6c/7d/9e/6c7d9e65-d9ff-2b1b-94f3-1434f0794423/Icon.png',
                        'partner' =>  'Katana',
                        'name' =>  'Spartan Wars1',
                        'payout' =>  1.55,
                        'device' =>  'iPhone',
                        'countries' =>  array('US'),
                        'is_incentive' =>  true,
                        'is_new' =>  true,
                        'relative_offers' => array($offerItem, )
                    )
                )
            ),
            array(
                'letter' => 'B',
                'offers' => array(
                    array(
                        'id' => 3,
                        'icon' =>  'http://a1008.phobos.apple.com/us/r30/Purple4/v4/6c/7d/9e/6c7d9e65-d9ff-2b1b-94f3-1434f0794423/Icon.png',
                        'partner' =>  'Satana',
                        'name' =>  'Spartan Hyis',
                        'payout' =>  1.55,
                        'device' =>  'iPhone',
                        'countries' =>  array('US'),
                        'is_incentive' =>  true,
                        'is_new' =>  true,
                        'relative_offers' => array($offerItem, $offerItem)
                    )
                )
            ),
        );


        $androidOffers = array(
            array(
                'letter' => 'C',
                'offers' => array(
                    array(
                        'id' => 4,
                        'icon' =>  'http://a1008.phobos.apple.com/us/r30/Purple4/v4/6c/7d/9e/6c7d9e65-d9ff-2b1b-94f3-1434f0794423/Icon.png',
                        'partner' =>  'Katana',
                        'name' =>  'Spartan Wars1',
                        'payout' =>  1.55,
                        'device' =>  'iPhone',
                        'countries' =>  array('US'),
                        'is_incentive' =>  true,
                        'is_new' =>  true,
                        'relative_offers' => array($offerItem, $offerItem)
                    ),
                    array(
                        'id' => 5,
                        'icon' =>  'http://a1008.phobos.apple.com/us/r30/Purple4/v4/6c/7d/9e/6c7d9e65-d9ff-2b1b-94f3-1434f0794423/Icon.png',
                        'partner' =>  'Katana',
                        'name' =>  'Spartan Wars1',
                        'payout' =>  1.55,
                        'device' =>  'iPhone',
                        'countries' =>  array('US'),
                        'is_incentive' =>  true,
                        'is_new' =>  true,
                        'relative_offers' => array($offerItem, $offerItem, $offerItem)
                    )
                )
            ),
            array(
                'letter' => 'D',
                'offers' => array(
                    array(
                        'id' => 6,
                        'icon' =>  'http://a1008.phobos.apple.com/us/r30/Purple4/v4/6c/7d/9e/6c7d9e65-d9ff-2b1b-94f3-1434f0794423/Icon.png',
                        'partner' =>  'Satana',
                        'name' =>  'Spartan Hyis',
                        'payout' =>  1.55,
                        'device' =>  'iPhone',
                        'countries' =>  array('US'),
                        'is_incentive' =>  true,
                        'is_new' =>  true,
                        'relative_offers' => array($offerItem)
                    )
                )
            ),
        );


        return new JsonResponse(
            array(
                'success'       => true,
                'iosOffers'     => $iosOffers,
                'androidOffers' => $androidOffers
            )
        );
    }



    /**
     * @Route("/edit/{id}", name="offer_delete")
     * @Method("GET")
     */
//    public function editOffer()
}
