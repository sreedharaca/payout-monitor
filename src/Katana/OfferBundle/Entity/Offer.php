<?php

namespace Katana\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Katana\AffiliateBundle\Entity\Affiliate as Affiliate;
use Katana\DictionaryBundle\Entity\Platform;


/**
 * Offer
 *
 * @ORM\Entity(repositoryClass="Katana\OfferBundle\Entity\OfferRepository")
 * @ORM\Table(name="offer",
 * indexes={
 *      @ORM\Index(name="active_idx", columns={"active"}),
 *      @ORM\Index(name="new_idx", columns={"new"}),
 *      @ORM\Index(name="incentive_idx", columns={"incentive"}),
 * },
 * uniqueConstraints={
 *     @ORM\UniqueConstraint(name="search_idx", columns={"external_id", "affiliate_id"})})
 * @ORM\HasLifecycleCallbacks
 */
class Offer
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
     * @ORM\Column(name="external_id", type="integer")
     */
    private $external_id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, options={"default":""} )
     */
    private $name;

    /**
     * @var decimal
     *
     * @ORM\Column(name="payout", type="decimal", precision=7, scale=2)
     */
    private $payout;

    /**
     * @var decimal
     *
     * @ORM\Column(name="preview_url", type="string", length=255, options={"nullable":true})
     */
    private $preview_url;

    /**
     * @var string
     *
     * @ORM\Column(name="final_url", type="string", length=255, nullable=true )
     */
    private $final_url;

    /**
     * @var datetime $created
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var boolean
     *
     * @ORM\Column(name="incentive", type="boolean", options={"default":"0"})
     */
    private $incentive;

    /**
     * @var boolean
     *
     * @ORM\Column(name="new", type="boolean", options={"default":"1"})
     */
    private $new;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", options={"default"="1"})
     */
    private $active;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", options={"default"="0"})
     */
    private $deleted;

    /**
     * @var boolean
     *
     * @ORM\Column(name="json", type="text", nullable=true, options={"nullable":true})
     */
    private $json;


//    /**
//     * @ORM\Column(name="status", type="string", columnDefinition="enum('valid', 'stop')", options={"default":"valid"})
//     */
//    private $status = Offer::STATUS_VALID;


    /**
     * @ORM\ManyToOne(targetEntity="Katana\AffiliateBundle\Entity\Affiliate", inversedBy="offers")
     * @ORM\JoinColumn(name="affiliate_id", referencedColumnName="id")
     */
    protected $affiliate;

    /**
     * @ORM\ManyToMany(targetEntity="Katana\DictionaryBundle\Entity\Device", inversedBy="offers", cascade={"persist"})
     * @ORM\JoinTable(name="offers_devices")
     **/
    private $devices;

    /**
     * @ORM\ManyToMany(targetEntity="Katana\DictionaryBundle\Entity\Country", inversedBy="offers", cascade={"persist"})
     * @ORM\JoinTable(name="offers_countries")
     **/
    private $countries;

    /**
     * @ORM\ManyToOne(targetEntity="Katana\DictionaryBundle\Entity\Platform", inversedBy="offers")
     * @ORM\JoinColumn(name="platform_id", referencedColumnName="id")
     */
    private $platform;

    /**
     * @ORM\ManyToOne(targetEntity="App", inversedBy="offers")
     * @ORM\JoinColumn(name="app_id", referencedColumnName="id")
     */
    protected $app;

    /**
     * @ORM\OneToMany(targetEntity="Katana\LogBundle\Entity\Log", mappedBy="offer", cascade={"remove"})
     */
    protected $logs;

    /**
     * @ORM\ManyToOne(targetEntity="Katana\DictionaryBundle\Entity\Letter", inversedBy="offers")
     * @ORM\JoinColumn(name="letter_id", referencedColumnName="id")
     */
    protected $letter;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->created = new \DateTime();

        /***
         * new
         */
        $this->setNew(true);
        /***
         * incentive
         */
        if( substr_count( strtolower($this->getName()), 'incent') ){
            $this->setIncentive(true);
        }
        else{
            $this->setIncentive(false);
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        /***
         * обновить статус оффера если ему более 1 суток
         */
        $now = new \DateTime();
        $now->modify("-1 day");

        if( $this->getCreated() <= $now ) {
            $this->setNew(false);
        }
    }

    public function __toString() {
        return $this->getName();
    }


    public  function findName(){

        $App = $this->getApp();

        if(!empty($App)){

            $name = $App->getName();

            if($name){
                return $name;
            }
        }

        return $this->getName();
    }


    public function betterThan(Offer $offer){

        if($this->getPayout() > $offer->getPayout()){
            return true;
        }

        return false;
    }

    public function isIos()
    {
        $Platform = $this->getPlatform();

        if(!is_object($Platform)){
            return false;
        }

        if( $Platform->getName() == Platform::IOS ){
            return true;
        }
        else{
            return false;
        }
    }

    public function isAndroid()
    {
        $Platform = $this->getPlatform();

        if(!is_object($Platform)){
            return false;
        }

        if( $Platform->getName() == Platform::ANDROID ){
            return true;
        }
        else{
            return false;
        }
    }


    public function isItunesPreviewUrl()
    {
        if( strpos($this->preview_url, 'itunes.apple.com') !== false ){
            return true;
        }

        return false;
    }

    public function isPlayGooglePreviewUrl()
    {
        if( strpos($this->preview_url, 'play.google.com') !== false ){
            return true;
        }

        return false;
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
     * Set title
     *
     * @param string $title
     * @return Offer
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set affiliate
     *
     * @param \Katana\AffiliateBundle\Entity\Affiliate $affiliate
     * @return Offer
     */
    public function setAffiliate(\Katana\AffiliateBundle\Entity\Affiliate $affiliate = null)
    {
        $this->affiliate = $affiliate;
    
        return $this;
    }

    /**
     * Get affiliate
     *
     * @return \Katana\AffiliateBundle\Entity\Affiliate
     */
    public function getAffiliate()
    {
        return $this->affiliate;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Offer
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
     * @return Offer
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
     * Set payout
     *
     * @param float $payout
     * @return Offer
     */
    public function setPayout($payout)
    {
        $this->payout = $payout;
    
        return $this;
    }

    /**
     * Get payout
     *
     * @return float 
     */
    public function getPayout()
    {
        return $this->payout;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
//        $this->name = 'empty';
        $this->devices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->countries = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add devices
     *
     * @param \Katana\DictionaryBundle\Entity\Device $devices
     * @return Offer
     */
    public function addDevice(\Katana\DictionaryBundle\Entity\Device $devices)
    {
        $this->devices[] = $devices;
    
        return $this;
    }

    /**
     * Remove devices
     *
     * @param \Katana\DictionaryBundle\Entity\Device $devices
     */
    public function removeDevice(\Katana\DictionaryBundle\Entity\Device $devices)
    {
        $this->devices->removeElement($devices);
    }

    /**
     * Get devices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDevices()
    {
        return $this->devices;
    }

    /**
     * Get countries
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Offer
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set new
     *
     * @param boolean $new
     * @return Offer
     */
    public function setNew($new)
    {
        $this->new = $new;
    
        return $this;
    }

    /**
     * Get new
     *
     * @return boolean 
     */
    public function getNew()
    {
        return $this->new;
    }

    /**
     * Set incentive
     *
     * @param boolean $incentive
     * @return Offer
     */
    public function setIncentive($incentive)
    {
        $this->incentive = $incentive;
    
        return $this;
    }

    /**
     * Get incentive
     *
     * @return boolean 
     */
    public function getIncentive()
    {
        return $this->incentive;
    }

    /**
     * Add countries
     *
     * @param \Katana\DictionaryBundle\Entity\Country $countries
     * @return Offer
     */
    public function addCountrie(\Katana\DictionaryBundle\Entity\Country $countries)
    {
        $this->countries[] = $countries;
    
        return $this;
    }

    /**
     * Remove countries
     *
     * @param \Katana\DictionaryBundle\Entity\Country $countries
     */
    public function removeCountrie(\Katana\DictionaryBundle\Entity\Country $countries)
    {
        $this->countries->removeElement($countries);
    }

    /**
     * Set app
     *
     * @param \Katana\OfferBundle\Entity\App $app
     * @return Offer
     */
    public function setApp(\Katana\OfferBundle\Entity\App $app = null)
    {
        $this->app = $app;
    
        return $this;
    }

    /**
     * Get app
     *
     * @return \Katana\OfferBundle\Entity\App 
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Set preview_url
     *
     * @param string $previewUrl
     * @return Offer
     */
    public function setPreviewUrl($previewUrl)
    {
        $this->preview_url = $previewUrl;
    
        return $this;
    }

    /**
     * Get preview_url
     *
     * @return string 
     */
    public function getPreviewUrl()
    {
        return $this->preview_url;
    }

    /**
     * Set platform
     *
     * @param \Katana\DictionaryBundle\Entity\Platform $platform
     * @return Offer
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

    /**
     * Set active
     *
     * @param boolean $active
     * @return Offer
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
     * Set json
     *
     * @param string $json
     * @return Offer
     */
    public function setJson($json)
    {
        $this->json = $json;
    
        return $this;
    }

    /**
     * Get json
     *
     * @return string 
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * Add logs
     *
     * @param \Katana\LogBundle\Entity\Log $logs
     * @return Offer
     */
    public function addLog(\Katana\LogBundle\Entity\Log $logs)
    {
        $this->logs[] = $logs;
    
        return $this;
    }

    /**
     * Remove logs
     *
     * @param \Katana\LogBundle\Entity\Log $logs
     */
    public function removeLog(\Katana\LogBundle\Entity\Log $logs)
    {
        $this->logs->removeElement($logs);
    }

    /**
     * Get logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Offer
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    
        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set letter
     *
     * @param \Katana\DictionaryBundle\Entity\Letter $letter
     * @return Offer
     */
    public function setLetter(\Katana\DictionaryBundle\Entity\Letter $letter = null)
    {
        $this->letter = $letter;
    
        return $this;
    }

    /**
     * Get letter
     *
     * @return \Katana\DictionaryBundle\Entity\Letter 
     */
    public function getLetter()
    {
        return $this->letter;
    }

    /**
     * Set final_url
     *
     * @param string $finalUrl
     * @return Offer
     */
    public function setFinalUrl($finalUrl)
    {
        $this->final_url = $finalUrl;
    
        return $this;
    }

    /**
     * Get final_url
     *
     * @return string 
     */
    public function getFinalUrl()
    {
        return $this->final_url;
    }
}