<?php
namespace Dellaert\WebappDeploymentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * @ORM\Entity
 * @ORM\Table(name="webdep_databasetype")
 */
class DatabaseType 
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * 
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     * 
     * @var boolean
     */
    protected $enabled;

    /**
     * @ORM\OneToMany(targetEntity="ApplicationTemplate", mappedBy="databaseType")
     */
    protected $applicationTemplates;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $code;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=255, unique=true)
     * 
     * @var string
     */
    protected $slug;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     */
    protected $pleskDBId;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     */
    protected $pleskDBhost;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->applicationTemplates = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function preInsert()
    {
        $this->preUpdate();
    }
    
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return DatabaseType
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return DatabaseType
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return DatabaseType
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
     * Set code
     *
     * @param string $code
     * @return DatabaseType
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return DatabaseType
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add applicationTemplates
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\ApplicationTemplate $applicationTemplates
     * @return DatabaseType
     */
    public function addApplicationTemplate(\Dellaert\WebappDeploymentBundle\Entity\ApplicationTemplate $applicationTemplates)
    {
        $this->applicationTemplates[] = $applicationTemplates;
    
        return $this;
    }

    /**
     * Remove applicationTemplates
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\ApplicationTemplate $applicationTemplates
     */
    public function removeApplicationTemplate(\Dellaert\WebappDeploymentBundle\Entity\ApplicationTemplate $applicationTemplates)
    {
        $this->applicationTemplates->removeElement($applicationTemplates);
    }

    /**
     * Get applicationTemplates
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getApplicationTemplates()
    {
        return $this->applicationTemplates;
    }

    /**
     * Set pleskDBId
     *
     * @param integer $pleskDBId
     * @return DatabaseType
     */
    public function setPleskDBId($pleskDBId)
    {
        $this->pleskDBId = $pleskDBId;
    
        return $this;
    }

    /**
     * Get pleskDBId
     *
     * @return integer 
     */
    public function getPleskDBId()
    {
        return $this->pleskDBId;
    }
}