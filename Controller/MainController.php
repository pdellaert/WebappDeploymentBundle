<?php
namespace Dellaert\WebappDeploymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function indexAction()
    {
        $this->get("breadcrumbs")
            ->add("Dashboard", $this->get("router")->generate("homepage"));
        return $this->render('DellaertWebappDeploymentBundle:Main:index.html.twig');
    }
}
