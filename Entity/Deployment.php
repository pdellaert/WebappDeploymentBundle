<?php
namespace Dellaert\WebappDeploymentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * @ORM\Entity
 * @ORM\Table(name="webdep_deployment")
 */
class Deployment
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
     * @ORM\ManyToOne(targetEntity="Application", inversedBy="deployments")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $application;

    /**
     * @ORM\ManyToOne(targetEntity="Server", inversedBy="deployments")
     * @ORM\JoinColumn(name="server_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $server;

    /**
     * @ORM\ManyToOne(targetEntity="ServerType", inversedBy="deployments")
     * @ORM\JoinColumn(name="servertype_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $serverType;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $hostname;

    /**
     * @ORM\Column(type="boolean")
     * 
     * @var boolean
     */
    protected $deployed;

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
    protected $pleskSubscriptionId = '';

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $pleskAdminUserId = '';

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $pleskAdminUserName = '';

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $pleskAdminUserPass = '';

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $pleskDBServerId = '';

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $pleskDBId = '';

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $pleskDBName = '';

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $pleskDBUserId = '';

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $pleskDBUserName = '';

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $pleskDBUserPass = '';

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $pleskDBHost = '';
    
    public function preInsert()
    {
        $this->preUpdate();
    }
    
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
}
