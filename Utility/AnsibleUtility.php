<?php
namespace Dellaert\WebappDeploymentBundle\Utility;

use Dellaert\WebappDeploymentBundle\Entity\Server;
use Dellaert\WebappDeploymentBundle\Entity\ServerType;

class AnsibleUtility {

	static public function generateHostsFile($container,$doctrine) {
        $entity = new Server();
        $servers = $doctrine->getRepository('DellaertWebappDeploymentBundle:Server')->findByEnabled(true);
        $hosts = '';
        foreach( $servers as $server ) {
            $hosts .= $server->getHost().' ansible_ssh_host='.$server->getIp().' ansible_ssh_port='.$server->getSshPort().' ansible_ssh_user='.$server->getSshUser().' ansible_ssh_private_key_file='.$server->getSshKeyPath()."\n";
        }

        $hostsFile = $container->getParameter('dellaert_webapp_deployment.data_dir').'/'.$container->getParameter('dellaert_webapp_deployment.ansible_subdir').'/'.$container->getParameter('dellaert_webapp_deployment.ansible_hosts_file');
        file_put_contents($hostsFile, $hosts);
    }

}
