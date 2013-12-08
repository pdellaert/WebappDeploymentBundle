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
     * @ORM\ManyToOne(targetEntity="DeploymentType", inversedBy="deployments")
     * @ORM\JoinColumn(name="deploymenttype_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $deploymentType;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
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
    protected $deployed = false;

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
     * @return Deployment
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
     * @return Deployment
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
     * Set hostname
     *
     * @param string $hostname
     * @return Deployment
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    
        return $this;
    }

    /**
     * Get hostname
     *
     * @return string 
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set deployed
     *
     * @param boolean $deployed
     * @return Deployment
     */
    public function setDeployed($deployed)
    {
        if( !isset($deployed) ) {
            $this->deployed = false;
        } else {
            $this->deployed = $deployed;
        }
    
        return $this;
    }

    /**
     * Get deployed
     *
     * @return boolean 
     */
    public function getDeployed()
    {
        return $this->deployed;
    }

    /**
     * Set pleskCapable
     *
     * @param boolean $pleskCapable
     * @return Deployment
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
     * Set pleskSubscriptionId
     *
     * @param string $pleskSubscriptionId
     * @return Deployment
     */
    public function setPleskSubscriptionId($pleskSubscriptionId)
    {
        if( !isset($pleskSubscriptionId) ) {
            $this->pleskSubscriptionId = '';
        } else {
            $this->pleskSubscriptionId = $pleskSubscriptionId;
        }
    
        return $this;
    }

    /**
     * Get pleskSubscriptionId
     *
     * @return string 
     */
    public function getPleskSubscriptionId()
    {
        return $this->pleskSubscriptionId;
    }

    /**
     * Set pleskAdminUserId
     *
     * @param string $pleskAdminUserId
     * @return Deployment
     */
    public function setPleskAdminUserId($pleskAdminUserId)
    {
        if( !isset($pleskAdminUserId) ) {
            $this->pleskAdminUserId = '';
        } else {
            $this->pleskAdminUserId = $pleskAdminUserId;
        }
    
        return $this;
    }

    /**
     * Get pleskAdminUserId
     *
     * @return string 
     */
    public function getPleskAdminUserId()
    {
        return $this->pleskAdminUserId;
    }

    /**
     * Set pleskAdminUserName
     *
     * @param string $pleskAdminUserName
     * @return Deployment
     */
    public function setPleskAdminUserName($pleskAdminUserName)
    {
        if( !isset($pleskAdminUserName) ) {
            $this->pleskAdminUserName = '';
        } else {
            $this->pleskAdminUserName = $pleskAdminUserName;
        }
    
        return $this;
    }

    /**
     * Get pleskAdminUserName
     *
     * @return string 
     */
    public function getPleskAdminUserName()
    {
        return $this->pleskAdminUserName;
    }

    /**
     * Set pleskAdminUserPass
     *
     * @param string $pleskAdminUserPass
     * @return Deployment
     */
    public function setPleskAdminUserPass($pleskAdminUserPass)
    {
        if( !isset($pleskAdminUserPass) ) {
            $this->pleskAdminUserPass = '';
        } else {
            $this->pleskAdminUserPass = $pleskAdminUserPass;
        }
    
        return $this;
    }

    /**
     * Get pleskAdminUserPass
     *
     * @return string 
     */
    public function getPleskAdminUserPass()
    {
        return $this->pleskAdminUserPass;
    }

    /**
     * Set pleskDBServerId
     *
     * @param string $pleskDBServerId
     * @return Deployment
     */
    public function setPleskDBServerId($pleskDBServerId)
    {
        if( !isset($pleskDBServerId) ) {
            $this->pleskDBServerId = '';
        } else {
            $this->pleskDBServerId = $pleskDBServerId;
        }
    
        return $this;
    }

    /**
     * Get pleskDBServerId
     *
     * @return string 
     */
    public function getPleskDBServerId()
    {
        return $this->pleskDBServerId;
    }

    /**
     * Set pleskDBId
     *
     * @param string $pleskDBId
     * @return Deployment
     */
    public function setPleskDBId($pleskDBId)
    {
        if( !isset($pleskDBId) ) {
            $this->pleskDBId = '';
        } else {
            $this->pleskDBId = $pleskDBId;
        }
    
        return $this;
    }

    /**
     * Get pleskDBId
     *
     * @return string 
     */
    public function getPleskDBId()
    {
        return $this->pleskDBId;
    }

    /**
     * Set pleskDBName
     *
     * @param string $pleskDBName
     * @return Deployment
     */
    public function setPleskDBName($pleskDBName)
    {
        if( !isset($pleskDBName) ) {
            $this->pleskDBName = '';
        } else {
            $this->pleskDBName = $pleskDBName;
        }
    
        return $this;
    }

    /**
     * Get pleskDBName
     *
     * @return string 
     */
    public function getPleskDBName()
    {
        return $this->pleskDBName;
    }

    /**
     * Set pleskDBUserId
     *
     * @param string $pleskDBUserId
     * @return Deployment
     */
    public function setPleskDBUserId($pleskDBUserId)
    {
        if( !isset($pleskDBUserId) ) {
            $this->pleskDBUserId = '';
        } else {
            $this->pleskDBUserId = $pleskDBUserId;
        }
    
        return $this;
    }

    /**
     * Get pleskDBUserId
     *
     * @return string 
     */
    public function getPleskDBUserId()
    {
        return $this->pleskDBUserId;
    }

    /**
     * Set pleskDBUserName
     *
     * @param string $pleskDBUserName
     * @return Deployment
     */
    public function setPleskDBUserName($pleskDBUserName)
    {
        if( !isset($pleskDBUserName) ) {
            $this->pleskDBUserName = '';
        } else {
            $this->pleskDBUserName = $pleskDBUserName;
        }
    
        return $this;
    }

    /**
     * Get pleskDBUserName
     *
     * @return string 
     */
    public function getPleskDBUserName()
    {
        return $this->pleskDBUserName;
    }

    /**
     * Set pleskDBUserPass
     *
     * @param string $pleskDBUserPass
     * @return Deployment
     */
    public function setPleskDBUserPass($pleskDBUserPass)
    {
        if( !isset($pleskDBUserPass) ) {
            $this->pleskDBUserPass = '';
        } else {
            $this->pleskDBUserPass = $pleskDBUserPass;
        }
    
        return $this;
    }

    /**
     * Get pleskDBUserPass
     *
     * @return string 
     */
    public function getPleskDBUserPass()
    {
        return $this->pleskDBUserPass;
    }

    /**
     * Set pleskDBHost
     *
     * @param string $pleskDBHost
     * @return Deployment
     */
    public function setPleskDBHost($pleskDBHost)
    {
        if( !isset($pleskDBHost) ) {
            $this->pleskDBHost = '';
        } else {
            $this->pleskDBHost = $pleskDBHost;
        }
    
        return $this;
    }

    /**
     * Get pleskDBHost
     *
     * @return string 
     */
    public function getPleskDBHost()
    {
        return $this->pleskDBHost;
    }

    /**
     * Set application
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\Application $application
     * @return Deployment
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
     * Set server
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\Server $server
     * @return Deployment
     */
    public function setServer(\Dellaert\WebappDeploymentBundle\Entity\Server $server = null)
    {
        $this->server = $server;
    
        return $this;
    }

    /**
     * Get server
     *
     * @return \Dellaert\WebappDeploymentBundle\Entity\Server 
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Set deploymentType
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\DeploymentType $deploymentType
     * @return Deployment
     */
    public function setDeploymentType(\Dellaert\WebappDeploymentBundle\Entity\DeploymentType $deploymentType = null)
    {
        $this->deploymentType = $deploymentType;
    
        return $this;
    }

    /**
     * Get deploymentType
     *
     * @return \Dellaert\WebappDeploymentBundle\Entity\DeploymentType 
     */
    public function getDeploymentType()
    {
        return $this->deploymentType;
    }
}