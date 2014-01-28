<?php

namespace Katana\SyncBundle\Lib\Curl;


/***
 * Class Curl
 *
 * Usage:
 * $curl = new Curl($options);
 * $curl->getContent();
 * $curl->getEffectiveUrl();
 */
class Curl {

    private $options = array();

    private $handler = null;

    private $content;

    private $effective_url;

    function __construct($options, $url = null)
    {
        if($url){
            $options[CURLOPT_URL] = $url;
        }

        $this->options = $options;

        $this->handler = curl_init();

        curl_setopt_array( $this->handler, $this->options );

        $this->content = curl_exec($this->handler);

        $this->effective_url = curl_getinfo($this->handler , CURLINFO_EFFECTIVE_URL);
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getEffectiveUrl()
    {
        return $this->effective_url;
    }
}