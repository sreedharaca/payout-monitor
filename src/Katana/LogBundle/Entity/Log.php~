<?php

namespace Katana\LogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Log
 *
 * @ORM\Table(name="log")
 * @ORM\Entity(repositoryClass="Katana\LogBundle\Entity\LogRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Log
{
    const ACTION_NEW = 'Новый';
    const ACTION_PAYOUT_CHANGE = 'Изменение ставки';
    const ACTION_STOP = 'Остановлен';

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
     * @ORM\Column(name="action", type="string", columnDefinition="enum('Новый', 'Изменение ставки', 'Остановлен')")
     */
    private $action;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="Katana\OfferBundle\Entity\Offer", inversedBy="logs", cascade={"persist"})
     * @ORM\JoinColumn(name="offer_id", referencedColumnName="id")
     */
    protected $offer;


    /**
     * Запускается перед созданием объекта
     *
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->created = new \DateTime();
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
     * Set action
     *
     * @param string $action
     * @return Log
     */
    public function setAction($action)
    {
        $this->action = $action;
    
        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Log
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Log
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
     * @return Log
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