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
            $hosts .= $server->getHost()."\n";
        }

        $hostsFile = $container->getParameter('dellaert_webapp_deployment.data_dir').'/'.$container->getParameter('dellaert_webapp_deployment.ansible_subdir').'/'.$container->getParameter('dellaert_webapp_deployment.ansible_hosts_file');
        file_put_contents($hostsFile, $hosts);
    }

}
