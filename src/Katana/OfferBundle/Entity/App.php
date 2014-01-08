<?php

namespace Katana\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App
 *
 * @ORM\Table(name="app",
 * indexes={@ORM\Index(name="external_id_idx", columns={"external_id"})},
 * uniqueConstraints={
 *     @ORM\UniqueConstraint(name="id_platform_idx", columns={"external_id", "platform"})})
 * @ORM\Entity(repositoryClass="Katana\OfferBundle\Entity\AppRepository")
 */
class App
{
    const PLATFORM_IOS = 'iOS';
    const PLATFORM_ANDROID = 'Android';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="external_id", type="string", length=255)
     */
    private $external_id;

    /**
     * @var string
     *
     * @ORM\Column(name="platform", type="string", nullable=true, columnDefinition="enum('iOS', 'Android')")
     */
    private $platform; // = App::PLATFORM_IOS;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true, options={"nullable":true})
     */
    private $name;

    /**
     * @ORM\Column(name="description", type="text", nullable=true, options={"nullable":true})
     */
    private $description;

    /**
     * @ORM\Column(name="icon_url", type="string", length=255, nullable=true, options={"nullable":true})
     */
    private $icon_url;

    /**
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="app")
     */
    private $offers;

//    /**
//     * @ORM\ManyToMany(targetEntity="Katana\DictionaryBundle\Entity\Platform", mappedBy="apps")
//     * @ORM\JoinTable(name="apps_platforms")
//     **/
//    private $platform_entity;

    /**
     * Set name
     *
     * @param string $name
     * @return App
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
     * Set external_id
     *
     * @param integer $externalId
     * @return App
     */
    public function setExternalId($externalId)
    {
        $this->external_id = $externalId;
    
        return $this;
    }

    /**
     * Get external_id
     *
     * @return integer 
     */
    public function getExternalId()
    {
        return $this->external_id;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return App
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set icon_url
     *
     * @param string $iconUrl
     * @return App
     */
    public function setIconUrl($iconUrl)
    {
        $this->icon_url = $iconUrl;
    
        return $this;
    }

    /**
     * Get icon_url
     *
     * @return string 
     */
    public function getIconUrl()
    {
        return $this->icon_url;
    }

    /**
     * Set platform
     *
     * @param string $platform
     * @return App
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;
    
        return $this;
    }

    /**
     * Get platform
     *
     * @return string 
     */
    public function getPlatform()
    {
        return $this->platform;
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
     * @return App
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

}