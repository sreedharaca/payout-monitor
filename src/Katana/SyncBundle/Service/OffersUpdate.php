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

    public function __construct(Container $container)
    {
        set_time_limit(0);
        ini_set('memory_limit','256M');

        $this->container = $container;
    }

    //TODO сделать обновление данных по шагам с логами
    //TODO чтобы ничего не ломалось, если что то сломалось пишем логи. Общий try catch
    /***
     * Task: Загрузить json данные из api в базу
     *
     * I
     */
    public function jsonApiToDb()
    {
        $em = $this->container->get('doctrine')->getManager();

        $CronLog = $this->container->get('CronLogService');

        $Affiliates = $em->getRepository('KatanaAffiliateBundle:Affiliate')->findAllAffiliates();

        foreach($Affiliates as $Affiliate)
        {
            $Affiliate->truncateJson();

            $url = $Affiliate->getApiUrl();

            try {
                /*** API request */
                $json = $this->requestApi($url, $decode = false);
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

            $Affiliate->setJson($json);

            $em->flush();

            echo "{$Affiliate->getName()}: got " . ((int)(strlen($json)/1024)) . " Kbytes\n";

            $CronLog->save('LOAD API JSON', "{$Affiliate->getName()}: got " . ((int)(strlen($json)/1024)) . " Kbytes \n");
        }

        $em->clear();
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
            // reset the EM and all aias
//            $this->container->set('doctrine.orm.entity_manager', null);
//            $this->container->set('doctrine.orm.default_entity_manager', null);
// get a fresh EM
//            $em = $this->container->get('doctrine')->getManager();

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

            $i = 0;
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

//                    if(!empty($row['platform']) && is_object($row['platform'])){
//                        $Offer->setPlatform($row['platform']);
//                    }

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
//                    $Countries = $em->getRepository('KatanaDictionaryBundle:Country')->findByOffer($Offer);

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


//                    if(!empty($row['platform']) && is_object($row['platform'])){
//                        $Offer->setPlatform($row['platform']);
//                    }

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

                    $em->persist($Offer);
//                    $em->flush();

                    $Log->save(Log::ACTION_NEW, '', $Offer);

                    $new_rows++;
                }

                $i++;

                if($i % 100 == 0){
                    $em->flush();
                }
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

        $em->clear();
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
            $data = null;
//echo "api ids: " . implode(',', $api_external_ids) . "\n";

            $db_external_ids = array();

            $affiliateOffers = $em->getRepository('KatanaOfferBundle:Offer')->findBy(array('affiliate'=>$Affiliate, 'deleted'=>'0'));
            foreach($affiliateOffers as $offer){
                $db_external_ids[] = $offer->getExternalId();
            }
            $affiliateOffers = null;
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

            $em->flush();
            $em->clear();
        }


    }

    public function resolveFinalUrlTask()
    {
echo "Resolve Final Url\n\n";
        $em = $this->container->get('Doctrine')->getManager();

        $Offers = $em->getRepository('KatanaOfferBundle:Offer')->findBy(array('final_url'=>null, 'active'=>1, 'deleted'=>0));

        $Curl = $this->container->get('CurlService');
$total = count($Offers);
$i = 0;
        foreach($Offers as $offer){

            if( $offer->isItunesPreviewUrl() || $offer->isPlayGooglePreviewUrl()  ){
                $finalUrl = $offer->getPreviewUrl();
            }
            else {
                $finalUrl = $Curl->catchRedirectUrl( str_replace('&amp;', '&', $offer->getPreviewUrl()) );
            }

            if($finalUrl){
                $offer->setFinalUrl($finalUrl);
            }

//if($finalUrl != $offer->getPreviewUrl()){
//    echo "  final: $finalUrl\npreview: {$offer->getPreviewUrl()}\n\n";
//}
            $i++;
//echo "$i/$total\n";

            if($i % 100 == 0){
                $em->flush();
            }
        }

        $em->flush();
    }

    /***
     * III
     */
    public function updateOffersPlatform()
    {
        echo "\nResolve Platform\n";

        $em = $this->container->get('Doctrine')->getManager();

        $Offers = $em->getRepository('KatanaOfferBundle:Offer')->findBy(array('platform'=>null, 'active'=>1, 'deleted'=>0));

        $PlatformService = $this->container->get('PlatformService');

//        $total = count($Offers);
        $i = 0;

        foreach($Offers as $Offer){

            $Platform = $PlatformService->guessByOffer($Offer);

            if(!empty($Platform)){

                $Offer->setPlatform($Platform);
//echo "{$Platform->getName()}\n";
            }

            if($i % 100 == 0){
                $em->flush();
            }

            $i++;
//            echo "$i/$total\n";
        }

        $em->flush();
    }


    /***
     * IV
     */
    public function tieOffersToApp()
    {
        echo "\nTie Offer to App\n";

        $em = $this->container->get('Doctrine')->getManager();

        $CronLog = $this->container->get('CronLogService');

        $Offers = $em->getRepository('KatanaOfferBundle:Offer')->findBy(array('app'=>null, 'active'=>1, 'deleted'=>0));

        $AppService = $this->container->get('AppService');

        $grouped_offers = 0;
        $new_apps = 0;

        $total = count($Offers);
        $i = 0;

        foreach($Offers as $Offer){

            $Platform = $Offer->getPlatform();

            if(empty($Platform)){
                continue;
            }

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


                //если новая аппа появилась сразу пишем в базу
                //чтобы следующие по очереди офферы могли подвязаться к этой аппе
                $em->flush();
            }

            if($i % 100 == 0){
                $em->flush();
            }
$i++;
//echo "$i/$total\n";
        }

        $em->flush();

        $CronLog->save('GROUP OFFERS', "Сгруппировано офферов: $grouped_offers. Новых Апп: $new_apps");
    }



    /***
     * V
     */
    public function loadItunesAppData()
    {
        echo "\n***Load Itunes App Data***\n";

        //получить все аппы из базы join с офферами where только iOS
        $em = $this->container->get('Doctrine')->getManager();
        $Offers = $em->getRepository('KatanaOfferBundle:Offer')->findByEmptyApps();

        $Itunes = $this->container->get('Itunes');

        //GET DISTINCT APPS
        $apps = array();

        foreach($Offers as $offer){

            $appid = $offer->getApp()->getId();
            $apps[$appid] = $offer->getApp();
        }


//        $total = count($apps);
        $i = 0;

        //LOAD DATA from ITUNES and  SET it to APP
        foreach($apps as $app){

            $itunes_id = $app->getExternalId();

            //запрос по айдишнику в айтюнс
            $data = $Itunes->get($itunes_id);
//echo "id: $itunes_id";
//var_dump($data);
//echo "\n";
            //обновляем данные аппы
            if($data != false){
                $app->setName($data['name']);
                $app->setIconUrl($data['iconUrl60']);
//echo "{$data['name']}:{$data['iconUrl60']}\n";
            }

            if($i % 100 == 0){
                $em->flush();
            }

$i++;
//echo "$i/$total\n";
        }

        $em->flush();
    }


    public function startSyncProcess()
    {
        echo "It works!\n\n";

        try {
echo "\n ===memory: ".memory_get_usage()/(1024*1024) . "\n";
            /*I*/       $this->jsonApiToDb();
echo "\n ===memory: ".memory_get_usage()/(1024*1024) . "\n";
            /*II.1*/    $this->updateOffers();
echo "\n ===memory: ".memory_get_usage()/(1024*1024) . "\n";
            /*II.2*/    $this->removeOffers();
echo "\n ===memory: ".memory_get_usage()/(1024*1024) . "\n";
            /**/        $this->resolveFinalUrlTask();
echo "\n ===memory: ".memory_get_usage()/(1024*1024) . "\n";
            /*III*/     $this->updateOffersPlatform();
echo "\n ===memory: ".memory_get_usage()/(1024*1024) . "\n";
            /*IV*/      $this->tieOffersToApp();
echo "\n ===memory: ".memory_get_usage()/(1024*1024) . "\n";
            /*V*/       $this->loadItunesAppData();
        }
        catch (\Exception $e){

            $Log = $this->container->get('CronLogService');

            $Log->save('FAIL !', $e->getMessage());
        }

    }


    /**
     * Запросить данные из API по партнерской ссылке
     *
     * @param $api урл
     * @param $decode вернеть json строку либо уже декодированную в array
     * @return array || string
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

}