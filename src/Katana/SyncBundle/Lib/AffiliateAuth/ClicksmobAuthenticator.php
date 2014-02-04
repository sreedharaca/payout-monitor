<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 31.01.14
 * Time: 6:51
 */

namespace Katana\SyncBundle\Lib\AffiliateAuth;

use Katana\AffiliateBundle\Entity\Affiliate;

class ClicksmobAuthenticator {

    private $container;

    function __construct($container)
    {
        $this->container = $container;
    }

    public function authenticate(Affiliate $Affiliate)
    {
        $url = 'http://clicksmob.com/analytics-new-api/login_api';
        $post_params = array('api_email' => 'silverwindmg@gmail.com', 'api_password' => 'rus37katana');

        $curl = $this->container->get('anchovy.curl');

        $curl->setURL($url);
        $curl->setMethod('POST', $post_params);

        $curl->setOptions(array(
            'CURLOPT_SSL_VERIFYHOST'=> 0,
            'CURLOPT_SSL_VERIFYPEER'=> 0,
            'CURLOPT_COOKIEJAR' => "cookie.txt",
            'CURLOPT_COOKIEFILE' => "cookie.txt"
        ));

        try{
            $content = $curl->execute();
        }
        catch(\Exception $e){
            echo $e->getMessage();
            return false;
        }

        return $this->isAuthOk($content);
    }


    public function isAuthOk($content)
    {
        if(substr_count($content, 'Ok.')>0){
            return true;
        }
        else{
            return false;
        }
    }

} 