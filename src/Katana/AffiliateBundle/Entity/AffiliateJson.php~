<?php

namespace Katana\AffiliateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AffiliateJson
 *
 * @ORM\Table(name="affiliate_json")
 * @ORM\Entity(repositoryClass="Katana\AffiliateBundle\Entity\AffiliateJsonRepository")
 */
class AffiliateJson
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
     * @ORM\Column(name="json", type="text", nullable=true)
     */
    private $json;


//    /**
//     * @ORM\OneToOne(targetEntity="Affiliate", inversedBy="affiliate_json")
//     * @ORM\JoinColumn(name="affiliate_id", referencedColumnName="id")
//     */
//    private $affiliate;

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
     * Set json
     *
     * @param string $json
     * @return AffiliateJson
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
     * Set affiliate
     *
     * @param \Katana\AffiliateBundle\Entity\Affiliate $affiliate
     * @return AffiliateJson
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
}