<?php

namespace Katana\SyncBundle\Service;

use Katana\AffiliateBundle\Entity\AffiliateJson;
use Katana\DictionaryBundle\Entity\Platform;
use Katana\OfferBundle\KatanaOfferBundle;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Katana\OfferBundle\Entity\Offer;
use Katana\OfferBundle\Entity\App;
use Katana\JsonParserBundle\Service\JsonParserManager;
use Katana\LogBundle\Entity\Log;
use Doctrine\ORM\ORMException;


class OffersUpdate
{
    private $container;

    public function __construct(Container $container) {
        set_time_limit(0);

        $this->container = $container;
    }

    //TODO сделать обновление данных по шагам с логами
    //TODO чтобы ничего не ломалось, если что то сломалось пишем логи. Общий try catch
    /***
     * I
     */
    public function jsonApiToDb()
    {
        $em = $this->container->get('doctrine')->getManager();

        $CronLog = $this->container->get('CronLogService');

        $Affiliates = $em->getRepository('KatanaAffiliateBundle:Affiliate')->findAllAffiliates();

        foreach($Affiliates as $Affiliate)
        {
            // reset the EM and all aias
            $container = $this->container;
            $container->set('doctrine.orm.entity_manager', null);
            $container->set('doctrine.orm.default_entity_manager', null);
// get a fresh EM
            $em = $this->container->get('doctrine')->getManager();

//            $Affiliate->setAffiliateJson(null);
            $AffiliateJsonOld = $Affiliate->getAffiliateJson();
            if(!empty($AffiliateJsonOld)){
                $AffiliateJsonOld->setJson(null);
            }

            $api = $Affiliate->getApiUrl();

            try {
                /*** API request */
                $json = $this->requestApi($api, $decode = false);
            }
            catch(\Exception $e){
                echo $e->getMessage(); //"Произошла ошибка при запросе данных через API Партнера: {$Affiliate->getName()}\n";
                $CronLog->save('LOAD API JSON', $e->getMessage());
                continue;  //TODO вести логи импорта данных
            }

            if(empty($json)){
                echo "EMPTY json from {$Affiliate->getName()}:  \n";

                $CronLog->save('LOAD API JSON', "EMPTY json from {$Affiliate->getName()}:  \n");

                continue;
            }


            $AffiliateJson = $Affiliate->getAffiliateJson();
            if(!empty($AffiliateJson)){
                $AffiliateJson->setJson($json);
            }
            else{
                $AffiliateJsonNew = new AffiliateJson();
                $AffiliateJsonNew->setJson($json);
                $Affiliate->setAffiliateJson($AffiliateJsonNew);

                $em->persist($AffiliateJsonNew);
//                $em->persist($AffiliateJsonNew);
            }

            $em->flush();
            $em->clear();

            echo "{$Affiliate->getName()}: got " . ((int)(strlen($json)/1024)) . " Kbytes\n";

            $CronLog->save('LOAD API JSON', "{$Affiliate->getName()}: got " . ((int)(strlen($json)/1024)) . " Kbytes \n");
        }

        //Log->save('connect Api', 'success', 'recieved json Affiliate Name')
    }

    /***
     * II.1
     */
    public function updateOffers()
    {
        $em = $this->container->get('doctrine')->getManager();

        $CronLog = $this->container->get('CronLogService');
        $Log = $this->container->get('LogService');

        //get all affiliates
        $Affiliates = $em->getRepository('KatanaAffiliateBundle:Affiliate')->findAllAffiliates();

        //loop affiliates
        foreach($Affiliates as $Affiliate)
        {
            $total_rows = 0;
            $update_rows = 0;
            $new_rows = 0;

            //get json data
            $AffiliateJson = $Affiliate->getAffiliateJson();

            if(empty($AffiliateJson)){
                continue;
            }

            $json = $AffiliateJson->getJson();

            if(empty($json)){
                continue;
            }

            //parse json to array
            $ParserManager = $this->container->get('json_parser_manager');
            $Parser = $ParserManager->getParser($Affiliate->getName());
            $data = $Parser->parse($json);

            if(empty($data)){
                echo "no Offers at " . $Affiliate->getName() . "\n";

                $CronLog->save('UPDATE OFFERS', "no Offers at " . $Affiliate->getName() . "\n");

                continue;
            }
//            var_dump($data);

            //loop array
            foreach($data as $row)
            {
                $total_rows ++;

                $Offer = $em->getRepository('KatanaOfferBundle:Offer')->findOneBy(
                    array(
                        'external_id' => $row['external_id'],
                        'affiliate' => $Affiliate )
                );

                /*** UPDATE Offer*/
                if( !empty($Offer) && is_object($Offer) )
                {
                    $Offer->setName($row['name']);

                    /***
                     * Запись события изменения цены
                     */
                    if($Offer->getPayout() != $row['payout']){
                        $Log->save(Log::ACTION_PAYOUT_CHANGE, "Было {$Offer->getPayout()} стало {$row['payout']}", $Offer);
                    }

                    $Offer->setPayout($row['payout']);
                    $Offer->setPreviewUrl($row['preview_url']); //TODO в базу пишется с заменами & => &amp;
//                    $Offer->setJson($row['json']);
                    $Offer->setActive(true);
                    $Offer->setDeleted(false);
                    $Offer->setJson(null);

                    if(!empty($row['platform']) && is_object($row['platform'])){
                        $Offer->setPlatform($row['platform']);
                    }

                    /***
                     * Update devices
                     */
                    $Devices = $em->getRepository('KatanaDictionaryBundle:Device')->findByOffer($Offer);

                    //remove old devices from offer
                    foreach($Offer->getDevices() as $device){
//                        $device->removeOffer($Offer);
                        $Offer->removeDevice($device);
                    }

                    //add new devices to offer
                    foreach($row['devices'] as $device){
//echo "adding new device #{$device->getId()} to offer #{$Offer->getId()} \n";
                        $Offer->addDevice($device);
                    }


                    /***
                     * Update countries
                     */
                    $Countries = $em->getRepository('KatanaDictionaryBundle:Country')->findByOffer($Offer);

                    //remove old countries from offer
//                    foreach($Countries as $country){
                    foreach($Offer->getCountries() as $country){
//echo "Remove Offer #{$Offer->getId()} from Country #{$country->getId()} \n";
//                        $country->removeOffer($Offer);
                        $Offer->removeCountrie($country);
                    }

                    //add new countries to offer
                    foreach($row['countries'] as $country){
//echo "Add Country #{$country->getId()} to Offer #{$Offer->getId()} \n";
                        $Offer->addCountrie($country);
                    }

                    $update_rows++;
                }
                /** INSERT offer */
                else
                {
                    $Offer = new Offer();
                    $Offer->setName($row['name']);
                    $Offer->setExternalId($row['external_id']);
                    $Offer->setAffiliate($Affiliate);
                    $Offer->setPayout($row['payout']);
                    $Offer->setPreviewUrl($row['preview_url']);
                    $Offer->setActive(true);
                    $Offer->setDeleted(false);
//                    $Offer->setJson($row['json']);
                    $Offer->setJson(null);


                    if(!empty($row['platform']) && is_object($row['platform'])){
                        $Offer->setPlatform($row['platform']);
                    }

                    /***
                     * Add devices
                     */
                    foreach($row['devices'] as $device)
                    {
                        $Offer->addDevice($device);
                    }

                    /***
                     * Add countries
                     */
                    foreach($row['countries'] as $country)
                    {
                        $Offer->addCountrie($country);
                    }

                    $Log->save(Log::ACTION_NEW, '', $Offer);

                    $new_rows++;
                }
                $em->persist($Offer);
            }
            $em->flush();

            ob_start();
            echo "-----------------------\n";
            echo "{$Affiliate->getName()}:\n";
            echo "Всего офферов: $total_rows \n";
            echo "Обновлено офферов: $update_rows \n";
            echo "Новых офферов: $new_rows \n";
            echo "=======================\n\n";
            ob_end_flush();

            $CronLog->save('UPDATE OFFERS', "{$Affiliate->getName()}: Всего офферов: $total_rows. Обновлено офферов: $update_rows. Новых офферов: $new_rows.");
        }//end: loop Affiliates

        //Log->save('Offer','updated', 'updated Offer #id')
        //Log->save('Offer','created', 'created Offer #id')
    }

    /***
     * II.2
     */
    public function removeOffers()
    {
        $em = $this->container->get('doctrine')->getManager();

        $CronLog = $this->container->get('CronLogService');
        $Log = $this->container->get('LogService');

        $Affiliates = $em->getRepository('KatanaAffiliateBundle:Affiliate')->findAllAffiliates();

        foreach($Affiliates as $Affiliate)
        {
echo "Processing {$Affiliate->getName()} \n";
            //get json data
            $AffiliateJson = $Affiliate->getAffiliateJson();

            if(empty($AffiliateJson)){
                continue;
            }

            $json = $AffiliateJson->getJson();

            if(empty($json)){
                continue;
            }

//            parse json to array
            $ParserManager = $this->container->get('json_parser_manager');
            $Parser = $ParserManager->getParser($Affiliate->getName());
            $data = $Parser->parse($json);

            if(empty($data)){
                continue;
            }

            $api_external_ids = array();

            foreach($data as $row){
                $api_external_ids[] = $row['external_id'];
            }
//echo "api ids: " . implode(',', $api_external_ids) . "\n";

            $db_external_ids = array();

            $affiliateOffers = $em->getRepository('KatanaOfferBundle:Offer')->findBy(array('affiliate'=>$Affiliate, 'deleted'=>'0'));
            foreach($affiliateOffers as $offer){
                $db_external_ids[] = $offer->getExternalId();
            }
//echo "db ids: " . implode(',', $db_external_ids) . "\n";

            $stop_ids = array_diff($db_external_ids, $api_external_ids);
echo "stop ids: " . implode(',', $stop_ids) . "\n\n\n";

            $em->getRepository('KatanaOfferBundle:Offer')->batchDeactivate($stop_ids, $Affiliate);

            if(count($stop_ids)){
                /***
                 * Запись события Остановка оффера
                 */
                foreach($stop_ids as $id){
                    $Offer = $em->getRepository('KatanaOfferBundle:Offer')->findOneBy(array('affiliate'=>$Affiliate, 'external_id'=>$id));
                    /*** Если оффер активен, то значит система узнала об удалении только что. Поэтому пишем событие. */
                    if($Offer->getActive()){
                        $Log->save(Log::ACTION_STOP, '', $Offer);
                    }

                }

                $CronLog->save('REMOVE OFFERS', "{$Affiliate->getName()}: Stop офферы: " . implode(', ', $stop_ids));
            }
        }

        $em->flush();
    }

    /***
     * III
     */
    public function updateOffersPlatform(){

        $em = $this->container->get('Doctrine')->getManager();

        $Offers = $em->getRepository('KatanaOfferBundle:Offer')->findBy(array('platform'=>null));

        $PlatformService = $this->container->get('PlatformService');

        foreach($Offers as $Offer){

            $Platform = $PlatformService->guessByOffer($Offer);

            if(!empty($Platform)){
                $Offer->setPlatform($Platform);
            }
        }

        $em->flush();
    }


    /***
     * IV
     */
    public function updateOffersApp(){

        $em = $this->container->get('Doctrine')->getManager();

        $CronLog = $this->container->get('CronLogService');

        $Offers = $em->getRepository('KatanaOfferBundle:Offer')->findBy(array('app'=>null));

        $grouped_offers = 0;
        $new_apps = 0;

        foreach($Offers as $Offer){

            $Platform = $Offer->getPlatform();

            if(empty($Platform)){
                continue;
            }

            $AppService = $this->container->get('AppService');

            $id = $AppService->parseIdByOffer($Offer);

            if(empty($id)){
//                echo "EMPTY id: $id \n";
                continue;
            }

            $App = $em->getRepository('KatanaOfferBundle:App')->findOneBy( array('external_id'=>$id) );

            if(!empty($App) && is_object($App)){

                echo "found app in db. TIE with offer. app id = $id\n";
                $Offer->setApp($App);

                $grouped_offers++;
            }
            else
            {
//echo $id . "\n";
                $App = new App();

                $App->setExternalId($id);
                $App->addOffer($Offer);

                $Offer->setApp($App);

                $em->persist($App);

                $grouped_offers++;
                $new_apps++;
            }

            $em->flush();
        }

        $CronLog->save('GROUP OFFERS', "Сгруппировано офферов: $grouped_offers. Новых Апп: $new_apps");
    }



    /***
     * V
     */
    public function updateAppsData()
    {
        //получить все аппы из базы join с офферами where только iOS
        //цикл по аппам
            //если нет названия и иконки
            //запрос по айдишнику в айтюнс
            //обновляем данные аппы


//        $ItunesService = $this->container->get('Itunes');


        //Log->save('App', 'created', 'created App #id')
        //Log->save('Offer', 'updated', 'Offer #id tied with App #id')
    }


    /***
     * Установить категорию офферу
     */

    public function tester()
    {
        echo "It works!\n\n";

//        /*I*/ $this->jsonApiToDb();
//      /*II.1*/  $this->updateOffers();
//      /*II.2*/  $this->removeOffers();
//      /*III*/ $this->updateOffersPlatform();
//        /*IV*/ $this->updateOffersApp();
//      /*V*/  $this->updateAppsData();


       $this->testLog();

//        $this->parseIdFromPreviewUrl();

//        $this->parsePreviewUrl();


//        $PlatformService = $this->container->get('PlatformService');
//        $Platform = $PlatformService->guessByRawString('Web Games');
//
//        var_dump($Platform->getName());

//        $result = $this->resolvePlatformByPreviewUrl("https://itunes.apple.com/app/id529996768?mt=8");
//        var_dump($result);


//        echo $url = 'http://app.appsflyer.com/com.dianxinos.dxbs?pid=yeahmobi_int&c=PH&clickid={transaction_id}&af_siteid={affiliate_id}';
//        echo "\n";
//
//        echo $final = $this->container->get('CurlService')->catchRedirectUrl($url);
//        echo "\n";

    }


    public function testLog(){

        $Log = $this->container->get('LogService');

        $Log->save(
            $action = Log::ACTION_NEW,
            $message = ''
        );

        $Log->save(
            $action = Log::ACTION_PAYOUT_CHANGE,
            $message = 'было: 0.30 стало 0.40'
        );

    }


    //    /***
//     * Распознать платформу оффера, там где ее нет
//     */
//    public function guessPlatform()
//    {
//        $em = $this->container->get('Doctrine')->getManager();
//        $Offers = $em->getRepository('KatanaOfferBundle:Offer')->findBy(array('platform'=>null));
//
//        foreach($Offers as $Offer){
//            /** try by preview url */
//            $Platform = $this->resolvePlatformByPreviewUrl($Offer->getPreviewUrl());
//            if(!empty($Platform)) {
//                $Offer->setPlatform($Platform);
//            }
//            /*** try by device */
//            else {
//                if( count($Offer->getDevices()) ){
//                    $Devices = $Offer->getDevices();
//
//                    $Platform = $Devices[0]->getPlatform();
//                    if( !empty($Platform) && is_object($Platform) ){
//                        $Offer->setPlatform($Platform);
//                    }
//                }
//            }
//        }
//
//        $em->flush();
//    }



//    /***
//     * По
//     */
//    public function parseIdFromPreviewUrl()
//    {
//        $em = $this->container->get('Doctrine')->getManager();
//
//        $Offers = $em->getRepository('KatanaOfferBundle:Offer')->findBy(array('app'=>null));
//
//        foreach($Offers as $Offer){
//
//            $Platform = $Offer->getPlatform();
//            if(empty($Platform)){
//                continue;
//            }
//
//            if($Platform == Platform::IOS){
//
//                $ItunesService = $this->container->get('Itunes');
//
//                $id = $ItunesService->parseUrl($Offer->getPreviewUrl());
//            }
//            elseif($Platform == Platform::ANDROID){
//                $PlayGoogleService = $this->container->get('PlayGoogle');
//
//                $id = $PlayGoogleService->parseUrl($Offer->getPreviewUrl());
////echo "parsed Google ID: $id \n";
//            }
//            else{
//                $id = false;
//            }
//
//            if(empty($id)){
//                continue;
//            }
//
//            $App = $em->getRepository('KatanaOfferBundle:App')->findOneBy( array('external_id'=>$id) );
//
//            if(!empty($App) && is_object($App)){
//
////echo "found app in db. TIE with offer. app id = $id\n";
//                //found: tie with offer
//                $Offer->setApp($App);
//            }
//            else
//            {
////echo $id . "\n";
//                //create App (tie with Offer)
//                $App = new App();
//
//                $App->setExternalId($id);
//                $App->addOffer($Offer);
//
//                $Offer->setApp($App);
//
//                $em->persist($App);
//            }
//
//            $em->flush();
//        }
//    }



//    private function resolvePlatformByPreviewUrl($url)
//    {
//        if( strpos($url, 'itunes.apple.com') !== false ){
//            $em = $this->container->get('doctrine')->getManager();
//            $Platform = $em->getRepository('KatanaDictionaryBundle:Platform')->findOneBy(array('name'=>Platform::IOS));
//            return $Platform;
//        }
//        elseif( strpos($url, 'play.google.com') !== false ){
//            $em = $this->container->get('doctrine')->getManager();
//            $Platform = $em->getRepository('KatanaDictionaryBundle:Platform')->findOneBy(array('name'=>Platform::ANDROID));
//            return $Platform;
//        }
//        else{
//            return false;
//        }
//    }

    public function startSyncProcess()
    {
        echo "It works!\n\n";

        try {
            /*I*/ $this->jsonApiToDb();
            /*II.1*/  $this->updateOffers();
            /*II.2*/  $this->removeOffers();
            /*III*/ $this->updateOffersPlatform();
            /*IV*/ $this->updateOffersApp();
    //      /*V*/  $this->updateAppsData();
        }
        catch (\Exception $e){

            $Log = $this->container->get('CronLogService');

            $Log->save('FAIL !', $e->getMessage());
        }

    }


    /**
     * @param $api
     * @return mixed
     */
    protected function requestApi($api, $decode = true)
    {
        $buzz = $this->container->get('buzz.browser');
        try{
            $response = $buzz->get($api);
        }
        catch(\Exception $e){
            throw new \Exception("Таймаут http запроса: $api.\n");
//            echo ;
//            return false;
        }

        $json = $json_raw = $response->getContent();

        if($decode){
            $json = json_decode($json_raw, true);
        }

        return $json;
    }


    public function show()
    {

        echo 'it works!';

        exit;

        $Offers = $this->container->get('doctrine')->getRepository('KatanaOfferBundle:Offer')->findAll();
        foreach ($Offers as $Offer)
        {
            echo $Offer->getName()." === ";
            echo (is_object($Offer->getApp())?$Offer->getApp()->getName():'      --- ');
            echo "\n";
        }
    }


    public function run(){
//        set_time_limit(0);
        echo 'it works!';
    }

//    protected function affiliate_store_offers($json)
//    {
//        $options = array('data', 'offers');
//
//        foreach ($options as $key) {
//            $json = $json[$key];
//        }
//
//        var_dump( $json );

//        $em = $this->container->get('doctrine')->getManager();
//
//        foreach ($json as $offer) {
//            echo "\"{$offer['name']}\"," . "\n";
//
//            create offer
//            $Offer = new Offer();
//            $Offer->setName($offer['name']);
//            $Offer->setPayment(0.00);
//
//            $em->persist($Offer);
//        }
//
//        $em->flush();
//
//    }



//    private function getAppDataFromItunesById( $id )
//    {
//        if ( ! $id ) {
//            return null;
//        }
//
//        $tuCurl = curl_init();
//        curl_setopt($tuCurl, CURLOPT_URL, "https://itunes.apple.com/lookup?id=$id");
//        //curl_setopt($tuCurl, CURLOPT_PORT , 443);
//        curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
//        curl_setopt($tuCurl, CURLOPT_HEADER, 0);
//        curl_setopt($tuCurl, CURLOPT_SSLVERSION, 3);
//        curl_setopt($tuCurl, CURLOPT_POST, 0);
//        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
//        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
//        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
//        //curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data);
//
//        $tuData = curl_exec($tuCurl);
//
//        if( curl_errno($tuCurl) ){
//            return null;
//        }
//
//        curl_close($tuCurl);
//
//        $result = json_decode( $tuData );
//
//        if( ! $result->resultCount ) {
//            return null;
//        }
//
//        $data = array();
//
//        $data['app_id']			= $id;
//        $data['name'] 			= $result->results[0]->trackName;//['trackName'];
//        $data['company'] 		= $result->results[0]->artistName;
//        $data['iconUrl60'] 		= $result->results[0]->artworkUrl60;
//        $data['rating'] 		= $result->results[0]->averageUserRating;
//        $data['ratingCount'] 	= $result->results[0]->userRatingCount;
//        $data['screenshotUrls'] = $result->results[0]->screenshotUrls;
//        $data['description'] 	= $result->results[0]->description;
//        $data['external_link']	= $result->results[0]->trackViewUrl;
//
//        return $data;

//    }



//    protected function sync_affiliate_katanaads()
//    {
//
//        $api = 'http://cpa.katanaads.com/offers/offers.json?api_key=AFFtX40vixsn9GbnqCZtM6MWxwPhnZ';
//
//        $json = $this->affiliate_get_data($api);
//
//        $this->affiliate_store_offers($json);

//        $this->container->get('doctrine')->getRepository('KatanaOfferBundle:App')->find(('app' => null));
//        $rep = $this->container->get('doctrine')->getRepository('KatanaOfferBundle:Offer');
//
//        $qb = $rep->createQueryBuilder("o");
//
//        $qb
//            ->where($qb->expr()->eq('o.app', ':app'))
//            ->setParameter('app', null);
//
//        $res = $qb->getResults();
//
//        var_dump($res);


    //$Offer->findAppExternalId()
    //$Offer->findAppName()

    //resolve app by offer
    //offer = ios || android

    // find App
//            if (strpos($offer['preview_url'], 'itunes')) {
//                if (preg_match('/.+id(\d+).*/', $offer['preview_url'], $matches)) {
//                    //var_dump($matches[1]);
//                    if (isset($matches[1])) {
//                        $itunes_id = (int)$matches[1];
//
//                        $App = $this->container->get('doctrine')->getRepository('KatanaOfferBundle:App')->findOneBy(array('itunes_id' => $itunes_id));
//                        if ($App instanceof App) {
//                            $Offer->setApp($App);
//                        } else {
//                            $App = new App();
//                            $App->setItunesId($itunes_id);
//                            $App->setName("");
//                            $App->setIcon("");
//
//                            //request to itunes API
////                            $Itunes = $this->container->get('itunes');
////                            $data = $Itunes->get($itunes_id);
//
//                            $data = $this->getAppDataFromItunesById($itunes_id);
////                            var_dump($data);
//
//                            if ($data) {
//                                $App->setName($data['name']);
//                            }
//
//                            $Offer->setApp($App);
//                        }
//
//                        $em->persist($App);
//                    }
//
//                }
//
//            }
//
//            $em->flush();
//    }

}