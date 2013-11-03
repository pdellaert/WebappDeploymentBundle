<?php
namespace Dellaert\WebappDeploymentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * @ORM\Entity
 * @ORM\Table(name="webdep_server")
 */
class Server
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
     * @ORM\ManyToMany(targetEntity="ServerType", inversedBy="servers")
     * @ORM\JoinTable(name="webdep_server_servertype", onDelete="CASCADE")
     */
    protected $serverTypes;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $host;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $ip;

    /**
     * @ORM\Column(type="integer", length=4)
     * @Assert\NotBlank()
     *
     * @var integer
     */
    protected $sshPort;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $sshUser;

    /**
     * @ORM\Column(type="boolean")
     * 
     * @var boolean
     */
    protected $pleskCapable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    protected $pleskUser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    protected $pleskPassword;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $sshKeyPath;

    /**
     * @Gedmo\Slug(fields={"host"})
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
        $this->serverTypes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Server
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
     * @return Server
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
     * Set host
     *
     * @param string $host
     * @return Server
     */
    public function setHost($host)
    {
        $this->host = $host;
    
        return $this;
    }

    /**
     * Get host
     *
     * @return string 
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Server
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    
        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set pleskCapable
     *
     * @param boolean $pleskCapable
     * @return Server
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
     * Set pleskUser
     *
     * @param string $pleskUser
     * @return Server
     */
    public function setPleskUser($pleskUser)
    {
        $this->pleskUser = $pleskUser;
    
        return $this;
    }

    /**
     * Get pleskUser
     *
     * @return string 
     */
    public function getPleskUser()
    {
        return $this->pleskUser;
    }

    /**
     * Set pleskPassword
     *
     * @param string $pleskPassword
     * @return Server
     */
    public function setPleskPassword($pleskPassword)
    {
        $this->pleskPassword = $pleskPassword;
    
        return $this;
    }

    /**
     * Get pleskPassword
     *
     * @return string 
     */
    public function getPleskPassword()
    {
        return $this->pleskPassword;
    }

    /**
     * Set sshKeyPath
     *
     * @param string $sshKeyPath
     * @return Server
     */
    public function setSshKeyPath($sshKeyPath)
    {
        $this->sshKeyPath = $sshKeyPath;
    
        return $this;
    }

    /**
     * Get sshKeyPath
     *
     * @return string 
     */
    public function getSshKeyPath()
    {
        return $this->sshKeyPath;
    }

    /**
     * Add serverTypes
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\ServerType $serverTypes
     * @return Server
     */
    public function addServerType(\Dellaert\WebappDeploymentBundle\Entity\ServerType $serverTypes)
    {
        $this->serverTypes[] = $serverTypes;
    
        return $this;
    }

    /**
     * Remove serverTypes
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\ServerType $serverTypes
     */
    public function removeServerType(\Dellaert\WebappDeploymentBundle\Entity\ServerType $serverTypes)
    {
        $this->serverTypes->removeElement($serverTypes);
    }

    /**
     * Get serverTypes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getServerTypes()
    {
        return $this->serverTypes;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Server
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
     * Set sshPort
     *
     * @param integer $sshPort
     * @return Server
     */
    public function setSshPort($sshPort)
    {
        $this->sshPort = $sshPort;
    
        return $this;
    }

    /**
     * Get sshPort
     *
     * @return integer 
     */
    public function getSshPort()
    {
        return $this->sshPort;
    }

    /**
     * Set sshUser
     *
     * @param string $sshUser
     * @return Server
     */
    public function setSshUser($sshUser)
    {
        $this->sshUser = $sshUser;
    
        return $this;
    }

    /**
     * Get sshUser
     *
     * @return string 
     */
    public function getSshUser()
    {
        return $this->sshUser;
    }
}