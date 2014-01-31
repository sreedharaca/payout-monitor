<?php
namespace Katana\SyncBundle\Lib\Loader;


class Url {

    private $url;

    private $parts = array();

    private $parameters = array();

    function __construct($url)
    {
        $this->url = $url;

        $this->parts = $this->parse($url);

        if(isset($this->parts['query'])){
            parse_str($this->parts['query'], $this->parameters);
        }
    }

    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    private function parse($url)
    {
        return parse_url($url);
    }

    public function getUrl()
    {
        return
            (isset($this->parts['scheme']) ? ($this->parts['scheme'])  . '://' : '') .
            $this->parts['host'] .
            (isset($this->parts['port']) ? (':' . $this->parts['port']) : '') .
            (isset($this->parts['path']) ? ('' . $this->parts['path']) : '') .
            (count($this->parameters)>0? '?' . http_build_query($this->parameters): '')
        ;
    }

} 