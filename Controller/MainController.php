<?php
namespace Dellaert\WebappDeploymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function indexAction()
    {
        return $this->render('DellaertDCIMBundle:Main:dashboard.html.twig');
    }
}
