<?php

namespace MD\DebateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MD\DebateBundle\Entity\Debate
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="MD\DebateBundle\Entity\DebateRepository")
 */
class Debate
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;
    
    /**
     * @var \DateTime $created
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\OneToMany(targetEntity="Contention", mappedBy="debate")
     */
    protected $contentions;

    protected $contentionsSorted;
    private $editable;

    public function __construct()
    {
        $this->contentions = new ArrayCollection();
        $this->contentionsSorted = new ArrayCollection();
        $this->editable = false;
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
     * @return Debate
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
     * Set description
     *
     * @param string $description
     * @return Debate
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
     * Add contentions
     *
     * @param MD\DebateBundle\Entity\Contention $contentions
     * @return Debate
     */
    public function addContention(\MD\DebateBundle\Entity\Contention $contentions)
    {
        $this->contentions[] = $contentions;
    
        return $this;
    }

    /**
     * Remove contentions
     *
     * @param MD\DebateBundle\Entity\Contention $contentions
     */
    public function removeContention(\MD\DebateBundle\Entity\Contention $contentions)
    {
        $this->contentions->removeElement($contentions);
    }

    /**
     * Get contentions
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getContentions()
    {
        return $this->contentions;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Debate
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


    public function getEditable() {
        return $this->editable;
    }
    public function setEditable($grant = false) {
        $this->editable = $grant;
        return $this;
    }

    public function sortContentions()
    {
        foreach ($this->contentions as $contention) {
            if ($contention->getAff()) {
                $this->contentionsSorted['aff'][] = $contention;
            }
            else {
                $this->contentionsSorted['neg'][] = $contention;
            }
        }
        unset($this->contentions);
        return $this;
    }

    /**
     * Method to update the Debate object based on changed values
     * passed in via a new prototype Debate object. Sets $edited time.
     *
     * @param $newDebate - A new Debate object whose values should overwrite this one's
     * @return $this: the new, updated debate
     */
    public function updateDebate(Debate $newDebate) {
        if ($name = $newDebate->getName()) {
            if (!empty($name)) {
                $this->setName($name);
            }
        }
        if ($description = $newDebate->getDescription()) {
            if (!empty($description)) {
                $this->setDescription($description);
            }
        }
        if ($contentions = $newDebate->getContentions()) {
            if (is_array($contentions)) {
                $this->contentions = $contentions;
            }
        }
        return $this;
    }
}