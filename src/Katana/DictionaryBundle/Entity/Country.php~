<?php

namespace Katana\DictionaryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country
 *
 * @ORM\Table( name="countries", indexes={@ORM\Index(name="code_idx", columns={"code"})} )
 * @ORM\Entity(repositoryClass="Katana\DictionaryBundle\Entity\CountryRepository")
 */
class Country
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
     * @ORM\Column(name="code", type="string", length=2)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Katana\OfferBundle\Entity\Offer", mappedBy="countries")
     * @ORM\JoinTable(name="offers_countries")
     **/
    private $offers;

//    /**
//     * @ORM\OneToMany(targetEntity="Katana\OfferBundle\Entity\OfferPayout", mappedBy="country")
//     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
//     */
//    protected $offer_payouts;

    public function __toString() {
        return $this->getName();
    }

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
     * Set code
     *
     * @param string $code
     * @return Country
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Country
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
     * @return Country
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