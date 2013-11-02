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
     * @ORM\ManyToOne(targetEntity="ApplicationTemplate", mappedBy="applications")
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
}
