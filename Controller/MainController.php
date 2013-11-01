<?php
namespace Dellaert\WebappDeploymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function indexAction()
    {
        $this->get("white_october_breadcrumbs")
            ->addItem("Dashboard", $this->get("router")->generate("homepage"));
        return $this->render('DellaertWebappDeploymentBundle:Main:index.html.twig');
    }

    public function helpAction()
    {
        $this->get("white_october_breadcrumbs")
            ->addItem("Help", $this->get("router")->generate("Help"));
        return $this->render('DellaertWebappDeploymentBundle:Main:help.html.twig');
    }
}
