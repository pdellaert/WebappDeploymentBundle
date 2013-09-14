<?php
namespace Dellaert\WebappDeploymentBundle\Entity;

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
	 * @ORM\JoinTable(name="webapp_server_servertype")
	 */
	protected $serverTypes;

	/**
	 * @ORM\Column(type="string", length=255)
	 * @Assert\NotBlank()
	 *
	 * @var string
	 */
	protected $host;

	/**
	 * @ORM\Column(type="string", length=255)
	 * @Assert\NotBlank()
	 *
	 * @var string
	 */
	protected $ip;

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
    public $sshKeyPath;

}