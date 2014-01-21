<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 18.01.14
 * Time: 22:06
 */

namespace Katana\OfferBundle\Lib;


use Katana\OfferBundle\Lib\LetterCategory;
use Katana\OfferBundle\Lib\OfferData;
use Katana\OfferBundle\Lib\App;


class AppAlphabetRepository {

    private $letter_categories = array();

    public function addApp(App $app)
    {
        //получить букву
        $letter = $app->getLetter();

        if(!$letter || !mb_check_encoding ( $letter, 'UTF-8' )){
            $letter = '?';
        }

        //добавить в соответствующий раздел
        $this->findLetterCategory($letter)->addApp($app);

        return $this;
    }

    private function findLetterCategory($letter)
    {
        if( !array_key_exists($letter, $this->letter_categories) ){
            $this->createLetterCategory($letter);
        }

        return $this->letter_categories[$letter];
    }

    private function createLetterCategory($letter)
    {
        $LC = new LetterCategory($letter);

        $this->letter_categories[$letter] = $LC;

        return $LC;
    }

    public function getLetterCategories()
    {
        return $this->letter_categories;
    }

    public function sort(){

        ksort($this->letter_categories);

        return $this;
    }

} 