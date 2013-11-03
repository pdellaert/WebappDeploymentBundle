<?php
namespace Dellaert\WebappDeploymentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * @ORM\Entity
 * @ORM\Table(name="webdep_applicationparametervalue")
 */
class ApplicationParameterValue
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
     * @ORM\ManyToOne(targetEntity="Application", inversedBy="applicationParameterValues")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", onDelete="CASCADE", onUpdate="CASCADE")
     */
    protected $application;

    /**
     * @ORM\ManyToOne(targetEntity="ApplicationTemplateParameter", inversedBy="applicationParameterValues")
     * @ORM\JoinColumn(name="applicationtemplateparameter_id", referencedColumnName="id", onDelete="CASCADE", onUpdate="CASCADE")
     */
    protected $ApplicationTemplateParameter;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $value;
    
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
     * @return ApplicationParameterValue
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
     * @return ApplicationParameterValue
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
     * Set value
     *
     * @param string $value
     * @return ApplicationParameterValue
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set application
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\Application $application
     * @return ApplicationParameterValue
     */
    public function setApplication(\Dellaert\WebappDeploymentBundle\Entity\Application $application = null)
    {
        $this->application = $application;
    
        return $this;
    }

    /**
     * Get application
     *
     * @return \Dellaert\WebappDeploymentBundle\Entity\Application 
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set ApplicationTemplateParameter
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\ApplicationTemplateParameter $applicationTemplateParameter
     * @return ApplicationParameterValue
     */
    public function setApplicationTemplateParameter(\Dellaert\WebappDeploymentBundle\Entity\ApplicationTemplateParameter $applicationTemplateParameter = null)
    {
        $this->ApplicationTemplateParameter = $applicationTemplateParameter;
    
        return $this;
    }

    /**
     * Get ApplicationTemplateParameter
     *
     * @return \Dellaert\WebappDeploymentBundle\Entity\ApplicationTemplateParameter 
     */
    public function getApplicationTemplateParameter()
    {
        return $this->ApplicationTemplateParameter;
    }
}