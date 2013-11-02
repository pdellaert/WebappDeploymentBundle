<?php
namespace Dellaert\WebappDeploymentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * @ORM\Entity
 * @ORM\Table(name="webdep_applicationtemplateparameter")
 */
class ApplicationTemplateParameter
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
     * @ORM\ManyToOne(targetEntity="ApplicationTemplate", inversedBy="applicationTemplateParameters")
     * @ORM\JoinColumn(name="applicationtemplate_id", referencedColumnName="id")
     */
    protected $applicationTemplate;

    /**
     * @ORM\OneToMany(targetEntity="ApplicationParameterValue", mappedBy="application")
     */
    protected $applicationParameterValues;

    /**
     * @ORM\Column(type="string", length=255)
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
    protected $isPassword;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->applicationParameterValues = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return ApplicationTemplateParameter
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
     * @return ApplicationTemplateParameter
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
     * @return ApplicationTemplateParameter
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
     * Set applicationTemplate
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\ApplicationTemplate $applicationTemplate
     * @return ApplicationTemplateParameter
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

    /**
     * Add applicationParameterValues
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\ApplicationParameterValue $applicationParameterValues
     * @return ApplicationTemplateParameter
     */
    public function addApplicationParameterValue(\Dellaert\WebappDeploymentBundle\Entity\ApplicationParameterValue $applicationParameterValues)
    {
        $this->applicationParameterValues[] = $applicationParameterValues;
    
        return $this;
    }

    /**
     * Remove applicationParameterValues
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\ApplicationParameterValue $applicationParameterValues
     */
    public function removeApplicationParameterValue(\Dellaert\WebappDeploymentBundle\Entity\ApplicationParameterValue $applicationParameterValues)
    {
        $this->applicationParameterValues->removeElement($applicationParameterValues);
    }

    /**
     * Get applicationParameterValues
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getApplicationParameterValues()
    {
        return $this->applicationParameterValues;
    }

    /**
     * Set isPassword
     *
     * @param boolean $isPassword
     * @return ApplicationTemplateParameter
     */
    public function setIsPassword($isPassword)
    {
        $this->isPassword = $isPassword;
    
        return $this;
    }

    /**
     * Get isPassword
     *
     * @return boolean 
     */
    public function getIsPassword()
    {
        return $this->isPassword;
    }
}