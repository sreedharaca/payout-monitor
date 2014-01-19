<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 17.01.14
 * Time: 10:15
 */

namespace Katana\OfferBundle\Lib;

use Katana\OfferBundle\Lib\LetterCategory;


class LetterCategoriesGroup {

    private $letter_categories = array();

    /**
     * @param array $letter_categories
     */
    public function addLetterCategory(LetterCategory $letter_category)
    {
        $letter = $letter_category->getLetter();

        if( array_key_exists($letter, $this->letter_categories) && is_array($this->letter_categories[$letter]) ){
            $this->letter_categories[$letter][] = $letter_category;
        }
        else{
            $this->letter_categories[$letter] = array($letter_category);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getLetterCategories()
    {
        return $this->letter_categories;
    }


    public function sort()
    {
        ksort($this->letter_categories);

        return $this;
    }
}