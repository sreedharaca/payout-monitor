<?php

namespace Katana\OfferBundle\Controller;

use Doctrine\ORM\Query\ResultSetMapping;
use Katana\OfferBundle\Lib\AppAlphabetRepository;
use Katana\OfferBundle\Lib\LetterCategoriesGroup;
use Katana\OfferBundle\Lib\LetterCategory;
use Katana\SyncBundle\Service\FinderManager\RedirectUrlFinderManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Katana\OfferBundle\Form\OfferFilterType;
use Katana\DictionaryBundle\Entity\Platform;
use Katana\OfferBundle\Lib\OfferGroup;
use Katana\OfferBundle\Lib\App;
use Katana\OfferBundle\Lib\OfferData;
use Katana\OfferBundle\Lib\FilterForm;
use Symfony\Component\HttpFoundation\Response;

/**
 * Offer Controller.
 *
 * @Route("/offer")
 */
class OfferController extends Controller
{
    /**
     *
     * @Route("/", name="offers")
     * @Method("GET")
     * @Template("KatanaOfferBundle:Offer:main.html.twig")
     */
    public function mainAction()
    {

        return array(
            'form' => $this->createForm(new OfferFilterType())->createView(),
        );
    }


//    /**
//     *
//     * @Route("/old", name="offers_old")
//     * @Method("GET")
//     * @Template("KatanaOfferBundle:Offer:index.html.twig")
//     */
//    public function indexAction()
//    {
//        $all_offers = $this->getDoctrine()->getRepository("KatanaOfferBundle:Offer")->findAllOffers();
////        $all_offers = $this->getDoctrine()->getRepository("KatanaOfferBundle:Offer")->findAll();
//
//        list($apps, $nonGroupedOffers) = $this->groupOffers($all_offers);
//
//        /*** FIND BEST PAYOUT  */
//        foreach($apps as $id => $app){
//            $sortedOffers = $this->sortOffersByPayoutAndGeo($app['offers']);
//
//            $bestOffer = array_pop($sortedOffers);
//            $apps[$id]['bestOffer'] = $bestOffer;
//            $apps[$id]['offers'] = $sortedOffers;
//        }
//
//        /*** SORT APPS */
//        $apps = $this->sortAppsByName($apps);
//
//
//        return array(
//            'form' => $this->createForm(new OfferFilterType())->createView(),
//            'nonGroupedOffers' => $nonGroupedOffers,
//            'apps' => $apps
//        );
//    }


//    /**
//     *
//     * @Route("/filter", name="filter_offers")
//     * @Method("POST")
//     * @Template("KatanaOfferBundle:Offer:index.html.twig")
//     */
//    public function filterOffersAction(Request $request)
//    {
//        $form = $this->createForm(new OfferFilterType());
//        $form->submit($request);
//
//        if($form->isValid())
//        {
//            $data = $form->getData();
//
//            $all_offers = $this->getDoctrine()->getRepository("KatanaOfferBundle:Offer")->getByFiltersData($data);
//
//            list($apps, $nonGroupedOffers) = $this->groupOffers($all_offers);
//
//            /*** FIND BEST PAYOUT  */
//            foreach($apps as $id => $app){
//                $sortedOffers = $this->sortOffersByPayoutAndGeo($app['offers']);
//
//                $bestOffer = array_pop($sortedOffers);
//                $apps[$id]['bestOffer'] = $bestOffer;
//                $apps[$id]['offers'] = $sortedOffers;
//            }
//
//            /*** SORT APPS */
//            $apps = $this->sortAppsByName($apps);
//        }
//
//
//        return array(
//            'form' => $form->createView(),
//            'nonGroupedOffers' => $nonGroupedOffers,
//            'apps' => $apps
//        );
//    }

//    private function groupOffers($all_offers)
//    {
//        /** Сгруппированные офферы */
//        /***
//         *  app_id:
//         *      'app': App
//         *      'offers': [Offers]
//         */
//        $apps = array();
//        /** Не сгруппированные офферы */
//        $nonGroupedOffers = array();
//
//        foreach($all_offers as $offer)
//        {
//            $App = $offer->getApp();
//
//            if( !empty( $App ) ){
//
//                $app_id = $offer->getApp()->getId();
//
//                if(!isset($apps[$app_id])) {
//                    $apps[$app_id] = array(
//                        'app'   => $offer->getApp(),
//                        'offers' => array()
//                    );
//                }
//
//                $apps[$app_id]['offers'][] = $offer;
//            }
//            else{
//                $nonGroupedOffers[] = $offer;
//            }
//        }
//
//        return array(
//            $apps,
//            $nonGroupedOffers
//        );
//    }

//    /***
//     * @param array
//     * $returns array
//     */
//    private function sortOffersByPayoutAndGeo($Offers)
//    {
//        usort($Offers, function($Offer1, $Offer2){
//
//            /** сортируем по возрастанию Payout */
//            if( $Offer1->getPayout() == $Offer2->getPayout() ){
//                /** Если ставка одинаковая то по широте ГЕО */
//                if( count($Offer1->getCountries()) == count($Offer2->getCountries()) ){
//                    return 0;
//                }
//
//                return (count($Offer1->getCountries()) < count($Offer2->getCountries())) ? -1 : 1;
//            }
//
//            return ($Offer1->getPayout() < $Offer2->getPayout()) ? -1 : 1;
//        });
//
//
//        return $Offers;
//    }

//    private function sortAppsByName($apps)
//    {
//        usort($apps, function($app1, $app2){
//
//            $name1 = $app1['app']->getName();
//            if(!strlen($app1['app']->getName())){
//                $name1 = $app1['bestOffer']->getName();
//            }
//
//            $name2 = $app2['app']->getName();
//            if(!strlen($app2['app']->getName())){
//                $name2 = $app2['bestOffer']->getName();
//            }
//
//            return strcasecmp($name1, $name2);
//        });
//
//        return $apps;
//    }

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


//    /**
//     * @Route("/delete", name="offer_delete")
//     * @Method("POST")
//     *
//     * , requirements={"id" = "\d+"}
//     */
//    public function deleteAction(Request $request)
//    {
//        $id = (int) $request->request->get('id');
//
//        if (!$id) {
//            throw $this->createNotFoundException('Не передан параметр id.');
//        }
//
//        $em = $this->getDoctrine()->getManager();
//
//        $offer = $em->getRepository('KatanaOfferBundle:Offer')->find($id);
//
//        if (!$offer || $offer->getDeleted()) {
//            throw $this->createNotFoundException('Оффер не найден.');
//        }
//
//        $offer->setDeleted(true);
//        $offer->setActive(false);
//
//        $em->flush();
//
//        return new JsonResponse(array('status' => 'ok'));
//    }


    /**
     * @Route("/ajax_filter", name="offer_ajax_filter")
     * @Method("POST")
     */
    public function ajaxFilterAction(Request $request)
    {
        $filter = new FilterForm();
        $filter->bind($request);


        $all_offers = $this->getDoctrine()->getRepository("KatanaOfferBundle:Offer")->getByAjaxData($filter->getData());

        //разделить на платформы
        $offersByPlatform = array(
            'iosOffers'     => $this->pickOutIos($all_offers),
            'androidOffers' => $this->pickOutAndroid($all_offers)
        );

        $data = array();

        foreach($offersByPlatform as $platform => $offers){

            //сгруппировать офферы по App
            //т.е. завернуть в OfferGroup
            $offer_groups = $this->groupByApp($offers); // app_id => OfferGroup

            //группы OfferGroup ---> App
            //завернуть в App
            $apps = array();

            foreach($offer_groups as $app_id => $OfferGroup){

                $apps[] = new App($OfferGroup);
            }

            //Apps ---> LetterCategory
            //разобрать App'ы по буквенным категориям - LetterCategory
            $AppRepo = new AppAlphabetRepository();

            foreach($apps as $app){
                $AppRepo->addApp($app);
            }

            $data[$platform] = $this->generateArrayData($AppRepo->sort());
        }


        return new JsonResponse(
            array(
                'success'       => true,
                'iosOffers'     => $data['iosOffers'],
                'androidOffers' => $data['androidOffers'],
                'names'         => $this->collectOffersNames($all_offers)
            )
        );
    }

    private function groupByApp($offers){

        $apps = array();

        foreach($offers as $offer)
        {
            $App = $offer->getApp();

            if( !empty( $App ) ){

                $app_id = $App->getId();

                if(!isset($apps[$app_id])) {
                    $apps[$app_id] = new OfferGroup();
                }

                $apps[$app_id]->addOffer($offer);
            }
        }

        return $apps;
    }


    private function generateArrayData(AppAlphabetRepository $AppRepo)
    {
        $letters = array();

        foreach($AppRepo->getLetterCategories() as $LC){

            $letter_offers = array();

            foreach($LC->getApps() as $app)
            {
                //Main Offer
                $OD = new OfferData($app->getMainOffer());
                $offer_data = $OD->toArray();

                $offer_data['relative_offers'] = $app->getOfferGroup()->toArray();

                $letter_offers[] = $offer_data;
            }

            $letter = $LC->getLetter();

            $letters[] = array(
                'letter' => $letter,
                'offers' => $letter_offers
            );

        }

        return $letters;
    }


    private function pickOutIos($offers){

        $ios = array();

        foreach($offers as $offer){

            if( $offer->isIos() ){
                $ios[] = $offer;
            }
        }

        return $ios;
    }

    private function pickOutAndroid($offers)
    {
        $android = array();

        foreach($offers as $offer){

            if( $offer->isAndroid() ){
                $android[] = $offer;
            }
        }

        return $android;
    }


    private function collectOffersNames($offers)
    {
        $names = array();

        foreach($offers as $offer){
            $names[] = $offer->findName();
        }

        $names = array_unique($names, SORT_REGULAR);

        $result = array();

        foreach($names as $name){
            $result[] = array('name' => $name);
        }

        return $result;
    }



    /*********** WEB OFFERS **********************************************************************************/

    /**
     *
     * @Route("/web", name="web_platform")
     * @Method("GET")
     * @Template("KatanaOfferBundle:Offer:web-platform.html.twig")
     */
    public function webPlatformAction()
    {

        $Platform = $this->getDoctrine()->getRepository("KatanaDictionaryBundle:Platform")->findByName(Platform::WEB);

        $form = $this->createForm(new OfferFilterType());
        $form->get('platform')->setData($Platform);

        return array(
            'form' => $form->createView(),
            'web_games_id' => $Platform[0]->getId()
        );
    }

    /**
     * @Route("/web_ajax_filter", name="offer_web_ajax_filter")
     * @Method("POST")
     */
    public function webAjaxFilterAction(Request $request)
    {
        $filter = new FilterForm();
        $filter->bind($request);

        $formData = $filter->getData();

        $Offset = $formData['offset'];


        /** GET MAX OFFERS IDS */
        $offer_ids = $this->getDoctrine()->getRepository('KatanaOfferBundle:Offer')->getMaxPayoutOffersIds();


        $paginator = $this->getDoctrine()->getRepository("KatanaOfferBundle:Offer")->getTest($filter->getData(), $Offset, $offer_ids);

        $data = $this->generateArrayData2($paginator);

        return new JsonResponse(
            array(
                'success'       => true,
                'offers'        => $data,
                'names'         => array(), //$this->collectOffersNames($all_offers),
                'totalCount'    => count($paginator),
                'offer_ids'     => count($offer_ids),
                'result offers count' => count($paginator)
            )
        );
    }

    private function generateArrayData2($paginator)
    {
        $offers = array();

        foreach($paginator->getIterator() as $row)
        {
            //Main Offer
            $OD = new OfferData($row);
            $offer_data = $OD->toArray();
            //Relative Offers
//            $offer_data['relative_offers'] = array();
//            $offer_data['relative_offers_count'] = $row['offers_in_app'] - 1;

            $offers[] = $offer_data;
        }

        return $offers;
    }

    /**
     *
     * @Route("/analogs", name="offer_analogs")
     * @Method("POST")
     */
    public function getAnalogs(Request $request)
    {
        $filter = new FilterForm();
        $filter->bind($request);

        $formData = $filter->getData();

        $offer_id = $formData['offer_id'];

        $em = $this->getDoctrine()->getManager();

        $Offer = $em->getRepository('KatanaOfferBundle:Offer')->find($offer_id);

        if(!$Offer){
            throw $this->createNotFoundException('Оффер не найден.');
        }

        $analogs = $em->getRepository('KatanaOfferBundle:Offer')->getAnalogs($Offer, $formData);

        $json_data = array();

        foreach($analogs as $offer)
        {
            $OD = new OfferData($offer);

            $json_data[] = $OD->toArray();
        }

        return new JsonResponse(
            array(
                'success' => true,
                'analogs' => $json_data,
                'formData'=> $formData
            )
        );
    }

    /**
     * @Route("/test", name="offer_test")
     * @Method("GET")
     * @Template("KatanaOfferBundle:Offer:test.html.twig")
     */
    public function testAction()
    {
        // assert get all offers filtered by post data
        $filter = new FilterForm();
        $filter->bind(new Request());

        $formData = $filter->getData();

        $formData['affiliate'] = 1;
        $formData['platform'] = array(1);


        $all_offers = $this->getDoctrine()->getRepository("KatanaOfferBundle:Offer")->getTest($formData);

        // group by app
        // find best offer
        $offer_groups = $this->groupByApp($all_offers);

        // get best offer ids
        $apps = array();

        foreach($offer_groups as $app_id => $OfferGroup)
        {
            $apps[] = new App($OfferGroup);
        }


        $offer_ids = array();

        foreach($apps as $app)
        {
            $offer_ids[] = $app->getMainOffer()->getId();
        }

        // select offers with joined data, sorted, limited
        $formData['sort_column'] = 'payout';
        $formData['sort_asc'] = false;

        $result_offers = $this->getDoctrine()->getRepository("KatanaOfferBundle:Offer")->SortAndLimitByOfferIds($offer_ids, $formData, 0);

        return array(
            'offers' => $result_offers
        );


    }

}
