<?php
namespace Dellaert\WebappDeploymentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * @ORM\Entity
 * @ORM\Table(name="webdep_application")
 */
class Application
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
     * @ORM\ManyToOne(targetEntity="ApplicationTemplate", inversedBy="applications")
     * @ORM\JoinColumn(name="applicationtemplate_id", referencedColumnName="id")
     */
    protected $applicationTemplate;

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
    protected $organisation;

    /**
     * @ORM\Column(type="boolean")
     * 
     * @var boolean
     */
    protected $pleskCapable;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $applicationUser;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $applicationPassword;

    /**
     * @Gedmo\Slug(fields={"name","organisation"})
     * @ORM\Column(length=255, unique=true)
     * 
     * @var string
     */
    protected $slug;
    
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
     * @return Application
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
     * @return Application
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
     * @return Application
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
     * Set organisation
     *
     * @param string $organisation
     * @return Application
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;
    
        return $this;
    }

    /**
     * Get organisation
     *
     * @return string 
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * Set pleskCapable
     *
     * @param boolean $pleskCapable
     * @return Application
     */
    public function setPleskCapable($pleskCapable)
    {
        $this->pleskCapable = $pleskCapable;
    
        return $this;
    }

    /**
     * Get pleskCapable
     *
     * @return boolean 
     */
    public function getPleskCapable()
    {
        return $this->pleskCapable;
    }

    /**
     * Set applicationUser
     *
     * @param string $applicationUser
     * @return Application
     */
    public function setApplicationUser($applicationUser)
    {
        $this->applicationUser = $applicationUser;
    
        return $this;
    }

    /**
     * Get applicationUser
     *
     * @return string 
     */
    public function getApplicationUser()
    {
        return $this->applicationUser;
    }

    /**
     * Set applicationPassword
     *
     * @param string $applicationPassword
     * @return Application
     */
    public function setApplicationPassword($applicationPassword)
    {
        $this->applicationPassword = $applicationPassword;
    
        return $this;
    }

    /**
     * Get applicationPassword
     *
     * @return string 
     */
    public function getApplicationPassword()
    {
        return $this->applicationPassword;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Application
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
     * Set applicationTemplate
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\ApplicationTemplate $applicationTemplate
     * @return Application
     */
    public function setApplicationTemplate(\Dellaert\WebappDeploymentBundle\Entity\ApplicationTemplate $applicationTemplate = null)
    {
        $this->applicationTemplate = $applicationTemplate;
    
        return $this;
    }

    /**
     * Get applicationTemplate
     *
     * @return \Dellaert\WebappDeploymentBundle\Entity\ApplicationTemplate 
     */
    public function getApplicationTemplate()
    {
        return $this->applicationTemplate;
    }
}