<?php

namespace Katana\DictionaryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Device
 *
 * @ORM\Table(name="devices", indexes={@ORM\Index(name="name_idx", columns={"name"})})
 * @ORM\Entity(repositoryClass="Katana\DictionaryBundle\Entity\DeviceRepository")
 */
class Device
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
     * @ORM\Column(name="name", type="string", length=20)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Katana\OfferBundle\Entity\Offer", mappedBy="devices")
     * @ORM\JoinTable(name="offers_devices")
     **/
    private $offers;

    /**
     * @ORM\ManyToOne(targetEntity="Platform", inversedBy="devices")
     * @ORM\JoinColumn(name="platform_id", referencedColumnName="id")
     */
    private $platform;

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
     * Set device
     *
     * @param string $device
     * @return Device
     */
    public function setName($device)
    {
        $this->name = $device;
    
        return $this;
    }

    /**
     * Get device
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
     * @return Device
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
     * Set platform
     *
     * @param \Katana\DictionaryBundle\Entity\Platform $platform
     * @return Device
     */
    public function setPlatform(\Katana\DictionaryBundle\Entity\Platform $platform = null)
    {
        $this->platform = $platform;
    
        return $this;
    }

    /**
     * Get platform
     *
     * @return \Katana\DictionaryBundle\Entity\Platform 
     */
    public function getPlatform()
    {
        return $this->platform;
    }
}