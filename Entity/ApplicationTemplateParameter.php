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
}
