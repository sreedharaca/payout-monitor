<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 17.01.14
 * Time: 10:12
 */

namespace Katana\OfferBundle\Lib;

use Katana\OfferBundle\Lib\App;


class LetterCategory {

    private $apps = array();

    private $letter;

    function __construct($letter)
    {
        $this->letter = $letter;
    }

    /**
     * @param array $apps
     */
    public function addApp(App $app)
    {
        $this->apps[] = $app;
    }

    /**
     * @return array
     */
    public function getApps()
    {
        return $this->apps;
    }

    /**
     * @param mixed $letter
     */
    public function setLetter($letter)
    {
        $this->letter = $letter;
    }

    /**
     * @return mixed
     */
    public function getLetter()
    {
        return $this->letter;
    }

}