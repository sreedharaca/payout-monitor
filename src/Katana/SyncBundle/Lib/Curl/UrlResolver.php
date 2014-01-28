<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 28.01.14
 * Time: 22:39
 */

namespace Katana\SyncBundle\Lib\Curl;


use Katana\SyncBundle\Lib\Curl\BaseRedirFinder;
use Katana\SyncBundle\Lib\Curl\Curl;


class UrlResolver {

    private $max_redirs = 3;
    private $redirs_performed = 0;

    private $curl_options = array();

    private $redir_finders = array();

    /***
     * Получает финальный урл по начальному, с учетом редиректов в теле html
     *
     * @param $startUrl
     * @return string $finalUrl
     */
    public function getFinalUrl($startUrl)
    {
        $curl = new Curl($this->curl_options, $startUrl);

        if($this->canRedir()){

            $content = $curl->getContent();

            $redirectUrl = $this->findRedirectUrl($content);

            if($redirectUrl !== false){

                $this->redirs_performed++;

                return $this->getFinalUrl($redirectUrl);
            }
        }

        return $curl->getEffectiveUrl();
    }

    public function setCurlOptions($curl_options)
    {
        $this->curl_options = $curl_options;
    }

    public function addFinder(BaseRedirFinder $finder)
    {
        $this->redir_finders[] = $finder;
    }

    protected function canRedir()
    {
        return ($this->redirs_performed < $this->max_redirs) ? true : false;
    }

    protected function findRedirectUrl($html)
    {
        foreach($this->redir_finders as $finder){

            $finder->setHtml($html);

            $url = $finder->findUrl();

            if($url !== false){
                return $url;
            }
        }

        return false;
    }
}