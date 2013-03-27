<?php

namespace MD\DebateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MD\DebateBundle\Entity\Point
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="MD\DebateBundle\Entity\PointRepository")
 */
class Point
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $body
     *
     * @ORM\Column(name="body", type="text")
     */
    private $body;

    /**
     * @var string $source
     *
     * @ORM\Column(name="source", type="string", length=255)
     */
    private $source;

    /**
     * @var \DateTime $created
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime $edited
     *
     * @ORM\Column(name="edited", type="datetime")
     */
    private $edited;

    /**
     * @ORM\ManyToOne(targetEntity="Contention", inversedBy="points")
     * @ORM\JoinColumn(name="contention_id", referencedColumnName="id")
     */
    protected $contention;

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
     * Set body
     *
     * @param string $body
     * @return Point
     */
    public function setBody($body)
    {
        $this->body = $body;
    
        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set source
     *
     * @param string $source
     * @return Point
     */
    public function setSource($source)
    {
        $this->source = $source;
    
        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Point
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
     * Set edited
     *
     * @param \DateTime $edited
     * @return Point
     */
    public function setEdited($edited)
    {
        $this->edited = $edited;
    
        return $this;
    }

    /**
     * Get edited
     *
     * @return \DateTime 
     */
    public function getEdited()
    {
        return $this->edited;
    }

    /**
     * Set contention
     *
     * @param MD\DebateBundle\Entity\Contention $contention
     * @return Point
     */
    public function setContention(\MD\DebateBundle\Entity\Contention $contention = null)
    {
        $this->contention = $contention;

        return $this;
    }

    /**
     * Get contention
     *
     * @return MD\DebateBundle\Entity\Contention
     */
    public function getContention()
    {
        return $this->contention;
    }

}
