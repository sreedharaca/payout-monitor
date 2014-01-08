<?php

namespace Katana\DictionaryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Letter
 *
 * @ORM\Table(name="letters")
 * @ORM\Entity(repositoryClass="Katana\DictionaryBundle\Entity\LetterRepository")
 */
class Letter
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=1)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Katana\OfferBundle\Entity\Offer", mappedBy="letter")
     */
    private $offers;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Letter
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->offers = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add offers
     *
     * @param \Katana\OfferBundle\Entity\Offer $offers
     * @return Letter
     */
    public function addOffer(\Katana\OfferBundle\Entity\Offer $offers)
    {
        $this->offers[] = $offers;
    
        return $this;
    }

    /**
     * Remove offers
     *
     * @param \Katana\OfferBundle\Entity\Offer $offers
     */
    public function removeOffer(\Katana\OfferBundle\Entity\Offer $offers)
    {
        $this->offers->removeElement($offers);
    }

    /**
     * Get offers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOffers()
    {
        return $this->offers;
    }
}