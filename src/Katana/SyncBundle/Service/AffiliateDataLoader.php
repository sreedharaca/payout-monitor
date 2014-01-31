<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 29.01.14
 * Time: 20:26
 */

namespace Katana\SyncBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Katana\AffiliateBundle\Entity\Affiliate;
use Katana\SyncBundle\Lib\Loader\RawDataCollection;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Katana\AffiliateBundle\Entity\RawData;
use Katana\SyncBundle\Lib\Loader\Url;


class AffiliateDataLoader {

    private $container;

    private $rows_per_request = 300;

    private $errors = array();

    private $browser;

    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->browser = $this->container->get('buzz.browser');
    }

    public function load(Affiliate $Affiliate)
    {
        if($Affiliate->isPartialLoading())
        {
            return $this->loadPartially($Affiliate);
        }
        else
        {
            return $this->loadFully($Affiliate);
        }
    }

    public function loadPartially(Affiliate $Affiliate)
    {
        $total_rows = $this->getTotalRows($Affiliate);

        if(!$total_rows){
            return new ArrayCollection();
        }

        //сколько нужно проходов
        $pages = ceil($total_rows / $this->rows_per_request);


        $Url = new Url($Affiliate->getApiUrl());

        $ArrayCollection = new ArrayCollection();
        //пройти
        for($i=1; $i<=$pages; $i++){
            $Url->setParameter('limit', $this->rows_per_request);
            $Url->setParameter('page', $i);

            $data = $this->request2($Url->getUrl());
            if(!$data){//если не удалось получилить данные из апи
                continue;
            }

            //сформировать коллекцию
            $RawData = new RawData();
            $RawData->setData($data);
            $RawData->setAffiliate($Affiliate);

            $ArrayCollection->add($RawData);
        }

        return $ArrayCollection;
    }

    public function loadFully(Affiliate $Affiliate)
    {
        $data = $this->request2($Affiliate->getApiUrl());
        if(!$data){ //если не удалось получилить данные из апи
            return new ArrayCollection();
        }
        //create object
        $RawData = new RawData();
        $RawData->setData($data);

        //add object to collection
        $ArrayCollection = new ArrayCollection();
        $ArrayCollection->add($RawData);
        $RawData->setAffiliate($Affiliate);

        return $ArrayCollection;
    }

//    private function request($url)
//    {
//        $headers = [
//            'CURLOPT_COOKIEJAR' => "cookie.txt",
//            'CURLOPT_COOKIEFILE' => "cookie.txt"
//        ];
//        //perform HTTP request
//        $response = $this->browser->get( $url, $headers );
//
//        //get data from response
//        return $response->getContent();
//    }

    private function request2($url)
    {
        //perform HTTP request
        $curl = $this->container->get('anchovy.curl');

        $curl->setURL($url);
        $curl->setMethod('GET', []);

        $curl->setOptions([
            'CURLOPT_SSL_VERIFYHOST'=> 0,
            'CURLOPT_SSL_VERIFYPEER'=> 0,
            'CURLOPT_TIMEOUT'    => 180,
            'CURLOPT_COOKIEJAR'  => "cookie.txt",
            'CURLOPT_COOKIEFILE' => "cookie.txt"
        ]);

        try{
            $content = $curl->execute();
        }
        catch(\Exception $e){
            echo "Во время запроса к апи произошла ошибка {$e->getMessage()}\n";
            return false;
        }

        //get data from response
        return $content;
    }

    //TODO действует только для одного формата данных (hasoffers)
    public function getTotalRows(Affiliate $Affiliate)
    {
        $Url = new Url($Affiliate->getApiUrl());
        $Url->setParameter('limit', 1);

        $data = $this->request2($Url->getUrl());

        $arr = json_decode($data, true);

        if( !isset($arr['total_rows']) || !intval($arr['total_rows'])>0 ){
            return false;
        }

        return $total_rows = intval($arr['total_rows']);
    }

    public function hasErrors()
    {
        if(count($this->errors)){
            return true;
        }

        return false;
    }


    public function getErrors()
    {
        return $this->errors;
    }
} 