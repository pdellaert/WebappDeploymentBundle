<?php
namespace Dellaert\WebappDeploymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * @ORM\Entity
 * @ORM\Table(name="webdep_servertype")
 */
class ServerType
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
     * @ORM\ManyToMany(targetEntity="Server", mappedBy="serverTypes")
     */
    protected $servers;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column(name="slug", type="string", length="255", unique=true)
     * @Gedmo\Slug(fields={"title"})
     * 
     * @var string
     */
    protected $slug;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->servers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return ServerType
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
     * @return ServerType
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
     * Set title
     *
     * @param string $title
     * @return ServerType
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get servers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getServers()
    {
        return $this->servers;
    }
    
    /**
     * Add servers
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\Server $servers
     * @return ServerType
     */
    public function addServer(\Dellaert\WebappDeploymentBundle\Entity\Server $servers)
    {
        $this->servers[] = $servers;
    
        return $this;
    }

    /**
     * Remove servers
     *
     * @param \Dellaert\WebappDeploymentBundle\Entity\Server $servers
     */
    public function removeServer(\Dellaert\WebappDeploymentBundle\Entity\Server $servers)
    {
        $this->servers->removeElement($servers);
    }
}