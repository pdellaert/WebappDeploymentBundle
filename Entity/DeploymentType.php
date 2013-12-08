<?php
namespace Dellaert\WebappDeploymentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * @ORM\Entity
 * @ORM\Table(name="webdep_deploymenttype")
 */
class DeploymentType
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
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    protected $gitBranch;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=255, unique=true)
     * 
     * @var string
     */
    protected $slug;

    /**
     * @ORM\OneToMany(targetEntity="Deployment", mappedBy="deploymentType")
     */
    protected $deployments;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->deployments = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return DeploymentType
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
     * @return DeploymentType
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
     * @return DeploymentType
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
     * Set slug
     *
     * @param string $slug
     * @return DeploymentType
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
     * Set code
     *
     * @param string $code
     * @return DeploymentType
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
     * Add deployments
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\Deployment $deployments
     * @return DeploymentType
     */
    public function addDeployment(\Dellaert\WebappDeploymentBundle\Entity\Deployment $deployments)
    {
        $this->deployments[] = $deployments;
    
        return $this;
    }

    /**
     * Remove deployments
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\Deployment $deployments
     */
    public function removeDeployment(\Dellaert\WebappDeploymentBundle\Entity\Deployment $deployments)
    {
        $this->deployments->removeElement($deployments);
    }

    /**
     * Get deployments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDeployments()
    {
        return $this->deployments;
    }
}