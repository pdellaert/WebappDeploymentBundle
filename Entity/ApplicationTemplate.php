<?php
namespace Dellaert\WebappDeploymentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * @ORM\Entity
 * @ORM\Table(name="webdep_applicationtemplate")
 */
class ApplicationTemplate 
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
     * @ORM\OneToMany(targetEntity="Application", mappedBy="applicationType")
     */
    protected $applications;

    /**
     * @ORM\OneToMany(targetEntity="ApplicationTemplateParameter", mappedBy="applicationTemplate")
     */
    protected $applicationTemplateParameters;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="boolean")
     * 
     * @var boolean
     */
    protected $databaseEnabled;

    /**
     * @ORM\ManyToOne(targetEntity="DatabaseType", inversedBy="applicationTemplates")
     * @ORM\JoinColumn(name="databasetype_id", referencedColumnName="id")
     */
    protected $databaseType;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $ansibleModule;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=255, unique=true)
     * 
     * @var string
     */
    protected $slug;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->applications = new \Doctrine\Common\Collections\ArrayCollection();
        $this->applicationTemplateParameters = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return ApplicationTemplate
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
     * @return ApplicationTemplate
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
     * @return ApplicationTemplate
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
     * Set databaseEnabled
     *
     * @param boolean $databaseEnabled
     * @return ApplicationTemplate
     */
    public function setDatabaseEnabled($databaseEnabled)
    {
        $this->databaseEnabled = $databaseEnabled;
    
        return $this;
    }

    /**
     * Get databaseEnabled
     *
     * @return boolean 
     */
    public function getDatabaseEnabled()
    {
        return $this->databaseEnabled;
    }

    /**
     * Set ansibleModule
     *
     * @param string $ansibleModule
     * @return ApplicationTemplate
     */
    public function setAnsibleModule($ansibleModule)
    {
        $this->ansibleModule = $ansibleModule;
    
        return $this;
    }

    /**
     * Get ansibleModule
     *
     * @return string 
     */
    public function getAnsibleModule()
    {
        return $this->ansibleModule;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return ApplicationTemplate
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
     * Add applications
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\Application $applications
     * @return ApplicationTemplate
     */
    public function addApplication(\Dellaert\WebappDeploymentBundle\Entity\Application $applications)
    {
        $this->applications[] = $applications;
    
        return $this;
    }

    /**
     * Remove applications
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\Application $applications
     */
    public function removeApplication(\Dellaert\WebappDeploymentBundle\Entity\Application $applications)
    {
        $this->applications->removeElement($applications);
    }

    /**
     * Get applications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Set databaseType
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\DatabaseType $databaseType
     * @return ApplicationTemplate
     */
    public function setDatabaseType(\Dellaert\WebappDeploymentBundle\Entity\DatabaseType $databaseType = null)
    {
        $this->databaseType = $databaseType;
    
        return $this;
    }

    /**
     * Get databaseType
     *
     * @return \Dellaert\WebappDeploymentBundle\Entity\DatabaseType 
     */
    public function getDatabaseType()
    {
        return $this->databaseType;
    }

    /**
     * Add applicationTemplateParameters
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\ApplicationTemplateParameter $applicationTemplateParameters
     * @return ApplicationTemplate
     */
    public function addApplicationTemplateParameter(\Dellaert\WebappDeploymentBundle\Entity\ApplicationTemplateParameter $applicationTemplateParameters)
    {
        $this->applicationTemplateParameters[] = $applicationTemplateParameters;
    
        return $this;
    }

    /**
     * Remove applicationTemplateParameters
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\ApplicationTemplateParameter $applicationTemplateParameters
     */
    public function removeApplicationTemplateParameter(\Dellaert\WebappDeploymentBundle\Entity\ApplicationTemplateParameter $applicationTemplateParameters)
    {
        $this->applicationTemplateParameters->removeElement($applicationTemplateParameters);
    }

    /**
     * Get applicationTemplateParameters
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getApplicationTemplateParameters()
    {
        return $this->applicationTemplateParameters;
    }
}