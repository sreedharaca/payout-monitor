<?php

namespace Katana\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PayoutHistory
 *
 * @ORM\Table(name="payout_history")
 * @ORM\Entity(repositoryClass="Katana\OfferBundle\Entity\PayoutHistoryRepository")
 */
class PayoutHistory
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
     * @var float
     *
     * @ORM\Column(name="payout", type="decimal", precision=7, scale=2)
     */
    private $payout;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


    /**
     * @ORM\ManyToOne(targetEntity="Katana\OfferBundle\Entity\Offer", inversedBy="payouts", cascade={"persist"})
     * @ORM\JoinColumn(name="offer_id", referencedColumnName="id")
     */
    protected $offer;


    public function  __construct( $payout = 0.00 )
    {
        $this->created = new \DateTime();

        $this->payout = $payout;
    }

    public function __toString()
    {
        return number_format($this->payout, 2);
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
     * Set payout
     *
     * @param float $payout
     * @return PayoutHistory
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
     * Set created
     *
     * @param \DateTime $created
     * @return PayoutHistory
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
     * Set offer
     *
     * @param \Katana\OfferBundle\Entity\Offer $offer
     * @return PayoutHistory
     */
    public function setOffer(\Katana\OfferBundle\Entity\Offer $offer = null)
    {
        $this->offer = $offer;
    
        return $this;
    }

    /**
     * Get offer
     *
     * @return \Katana\OfferBundle\Entity\Offer 
     */
    public function getOffer()
    {
        return $this->offer;
    }
}