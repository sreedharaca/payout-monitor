<?php

namespace Katana\AffiliateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Affiliate
 *
 * @ORM\Table(name="affiliate")
 * @ORM\Entity(repositoryClass="Katana\AffiliateBundle\Entity\AffiliateRepository")
 */
class Affiliate
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="api_url", type="string", length=255)
     */
    private $apiUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="offer_url", type="string", length=255)
     */
    private $offerUrl;

    /**
     * @ORM\OneToOne(targetEntity="AffiliateJson")
     * @ORM\JoinColumn(name="affiliate_json_id", referencedColumnName="id")
     */
    private $affiliate_json;

    /**
     * @var
     * @ORM\Column(name="active", type="boolean", options={"default":"1"})
     */
    private $active;
    
    /**
     * @ORM\OneToMany(targetEntity="Katana\OfferBundle\Entity\Offer", mappedBy="affiliate")
     * @ ORM\JoinColumn(name="offer_id", referencedColumnName="id")
     */
    protected $offers;


    public function __toString() {
        return $this->getName();
    }

    public function truncateJson()
    {
        $Json = $this->getAffiliateJson();

        if(!empty($Json)){
            $Json->setJson(null);
        }

        return $this;
    }

    public function setJson($json)
    {
        $AffJson = $this->getAffiliateJson();

        if( !empty($AffJson) ){
            $AffJson->setJson($json);
        }

        return $this;
    }

    public function createAffiliateJson()
    {
        $AffiliateJson = new AffiliateJson();

        $this->setAffiliateJson($AffiliateJson);

        return $AffiliateJson;
    }


    public function generateOfferUrl($offerId)
    {
        return str_replace('{offer_id}', $offerId, $this->getOfferUrl());
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
     * Set name
     *
     * @param string $name
     * @return Affiliate
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
     * Set apiUrl
     *
     * @param string $apiUrl
     * @return Affiliate
     */
    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    
        return $this;
    }

    /**
     * Get apiUrl
     *
     * @return string 
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->offers = new \Doctrine\Common\Collections\ArrayCollection();

        $this->affiliate_json = new AffiliateJson();
    }
    
    /**
     * Add offers
     *
     * @param \Katana\OfferBundle\Entity\Offer $offers
     * @return Affiliate
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

    /**
     * Set active
     *
     * @param boolean $active
     * @return Affiliate
     */
    public function setActive($active)
    {
        $this->active = $active;
    
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set affiliate_json
     *
     * @param \Katana\AffiliateBundle\Entity\AffiliateJson $affiliateJson
     * @return Affiliate
     */
    public function setAffiliateJson(\Katana\AffiliateBundle\Entity\AffiliateJson $affiliateJson = null)
    {
        $this->affiliate_json = $affiliateJson;
    
        return $this;
    }

    /**
     * Get affiliate_json
     *
     * @return \Katana\AffiliateBundle\Entity\AffiliateJson 
     */
    public function getAffiliateJson()
    {
        return $this->affiliate_json;
    }

    /**
     * Set offerUrl
     *
     * @param string $offerUrl
     * @return Affiliate
     */
    public function setOfferUrl($offerUrl)
    {
        $this->offerUrl = $offerUrl;
    
        return $this;
    }

    /**
     * Get offerUrl
     *
     * @return string 
     */
    public function getOfferUrl()
    {
        return $this->offerUrl;
    }
}