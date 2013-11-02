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
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     */
    protected $application;

    /**
     * @ORM\ManyToOne(targetEntity="ApplicationTemplateParameter", inversedBy="applicationParameterValues")
     * @ORM\JoinColumn(name="applicationtemplateparameter_id", referencedColumnName="id")
     */
    protected $ApplicationTemplateParameter;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
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
}
