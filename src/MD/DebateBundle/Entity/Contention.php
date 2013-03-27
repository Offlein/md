<?php

namespace MD\DebateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MD\DebateBundle\Entity\Contention
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="MD\DebateBundle\Entity\ContentionRepository")
 */
class Contention
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var boolean $aff
     *
     * @ORM\Column(name="aff", type="boolean")
     */
    private $aff;

    /**
     * @ORM\ManyToOne(targetEntity="MD\AccessBundle\Entity\User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     */
    protected $creator;

    /**
     * @ORM\ManyToOne(targetEntity="MD\AccessBundle\Entity\User")
     * @ORM\JoinColumn(name="editor_id", referencedColumnName="id")
     */
    protected $editor;

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
     * @ORM\ManyToOne(targetEntity="Debate", inversedBy="contentions")
     * @ORM\JoinColumn(name="debate_id", referencedColumnName="id")
     */
    protected $debate;

    /**
     * @ORM\OneToMany(targetEntity="Point", mappedBy="contention")
     */
    protected $points;

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
     * @return Contention
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
     * Set created
     *
     * @param \DateTime $created
     * @return Contention
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
     * Set debate
     *
     * @param MD\DebateBundle\Entity\Debate $debate
     * @return Contention
     */
    public function setDebate(\MD\DebateBundle\Entity\Debate $debate = null)
    {
        $this->debate = $debate;
    
        return $this;
    }

    /**
     * Get debate
     *
     * @return MD\DebateBundle\Entity\Debate 
     */
    public function getDebate()
    {
        return $this->debate;
    }

    /**
     * Add points
     *
     * @param MD\DebateBundle\Entity\Point $points
     * @return Contention
     */
    public function addPoint(\MD\DebateBundle\Entity\Point $points)
    {
        $this->points[] = $points;

        return $this;
    }

    /**
     * Remove points
     *
     * @param MD\DebateBundle\Entity\Point $points
     */
    public function removePoint(\MD\DebateBundle\Entity\Point $points)
    {
        $this->points->removeElement($points);
    }

    /**
     * Get points
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getPoints()
    {
        return $this->points;
    }

    public function countPoints()
    {
        return count($this->getPoints());
    }

    /**
     * Set aff
     *
     * @param boolean $aff
     * @return Contention
     */
    public function setAff($aff)
    {
        $this->aff = $aff;
    
        return $this;
    }

    /**
     * Get aff
     *
     * @return boolean 
     */
    public function getAff()
    {
        return $this->aff;
    }

    /**
     * Set creator
     *
     * @param MD\DebateBundle\Entity\User $creator
     * @return Contention
     */
    public function setCreator(\MD\DebateBundle\Entity\User $creator = null)
    {
        $this->creator = $creator;
    
        return $this;
    }

    /**
     * Get creator
     *
     * @return MD\DebateBundle\Entity\User 
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set editor
     *
     * @param MD\DebateBundle\Entity\User $editor
     * @return Contention
     */
    public function setEditor(\MD\DebateBundle\Entity\User $editor = null)
    {
        $this->editor = $editor;
    
        return $this;
    }

    /**
     * Get editor
     *
     * @return MD\DebateBundle\Entity\User 
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * Set edited
     *
     * @param \DateTime $edited
     * @return Contention
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
     * Method to update the Contention object based on changed values
     * passed in via a new prototype Contention object. Sets $edited time.
     *
     * @param $newContention - A new Contention object whose values should overwrite this one's
     * @return $this: the new, updated debate
     */
    public function updateContention(Contention $newContention) {
        if ($name = $newContention->getName()) {
            if (!empty($name)) {
                $this->setName($name);
            }
        }
        if ($aff = $newContention->getAff()) {
            if (!empty($aff)) {
                $this->setAff($aff);
            }
        }
        if ($debate = $newContention->getDebate()) {
            if (!empty($debate)) {
                $this->setDebate($debate);
            }
        }
        $this->setEdited(new \DateTime('now'));
        return $this;
    }
}