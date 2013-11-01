<?php
namespace Dellaert\WebappDeploymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dellaert\WebappDeploymentBundle\Entity\Server;
use Dellaert\WebappDeploymentBundle\Entity\ServerType;
use Symfony\Component\HttpFoundation\Response;

class ServerController extends Controller
{
    public function listAction()
    {
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Servers", $this->get("router")->generate("ServerList"));
        return $this->render('DellaertWebappDeploymentBundle:Server:list.html.twig');
    }
    
    public function listDataAction()
    {
        $request = $this->getRequest();
        
        $page = 1;
        if( $request->request->get('page') != null && $request->request->get('page') != '' ) {
            $page = $request->request->get('page');
        }
        
        $sortname = 'host';
        if( $request->request->get('sortname') != null && $request->request->get('sortname') != '' ) {
            $sortname = $request->request->get('sortname');
        }
        
        $sortorder = 'asc';
        if( $request->request->get('sortorder') != null && $request->request->get('sortorder') != '' ) {
            $sortorder = $request->request->get('sortorder');
        }
        
        $searchtype = '';
        if( $request->request->get('qtype') != null && $request->request->get('qtype') != '' ) {
            $searchtype = $request->request->get('qtype');
        }
        
        $searchquery = '';
        if( $request->request->get('query') != null && $request->request->get('query') != '' ) {
            $searchquery = $request->request->get('query');
        }
        
        $rp = 20;
        if( $request->request->get('rp') != null && $request->request->get('rp') != '' ) {
            $rp = $request->request->get('rp');
        }
        
        $pageStart = ($page-1)*$rp;
        
        $repository = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:Server');
        $qb = $repository->createQueryBuilder('c');
        if( $searchquery != '' && $searchtype != '' ) {
            $qb->add('where',$qb->expr()->like('c.'.$searchtype, $qb->expr()->literal('%'.$searchquery.'%')));
        }
        $qb->orderBy('c.'.$sortname,$sortorder);
        $qb->setFirstResult($pageStart);
        $qb->setMaxResults($rp);
        $query = $qb->getQuery();
        $results = $query->getResult();
        
        $em = $this->getDoctrine()->getEntityManager();
        $qstring = 'SELECT COUNT(c.id) FROM DellaertWebappDeploymentBundle:Server c';
        if( $searchquery != '' && $searchtype != '' ) {
            $qstring .= ' where '.$qb->expr()->like('c.'.$searchtype, $qb->expr()->literal('%'.$searchquery.'%'));
        }
        $query = $em->createQuery($qstring);
        $total = $query->getSingleScalarResult();
        
        $data['page'] = $page;
        $data['total'] = $total;
        $data['rows'] = array();
        foreach($results as $entity) {
            $pleskCapable = "no";
            if($entity->getPleskCapable()) {
                $pleskCapable = "yes";
            }
            $serverTypes = "";
            foreach( $entity->getServerTypes() as $serverType ) {
                $serverTypes .= $serverType->getName().', ';
            }
            $serverTypes = substr($serverTypes, 0, -2);
            $data['rows'][] = array(
                'id' => $entity->getSlug(),
                'cell' => array($entity->getHost(),$entity->getIp(),$serverTypes,$pleskCapable)
            );
        }
        
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
    
    public function viewAction($slug)
    {
        $repository = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:Server');
        $entity = $repository->findOneBySlug($slug);
        
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Servers", $this->get("router")->generate("ServerList"));
        if( $entity ) {
            $this->get("white_october_breadcrumbs")->addItem($entity->getHost(), $this->get("router")->generate("ServerViewSlug",array('slug'=>$slug)));
        } else {
            $this->get("white_october_breadcrumbs")->addItem("Unkown server", '');
        }
        
        return $this->render('DellaertWebappDeploymentBundle:Server:view.html.twig',array('entity'=>$entity));
    }
    
    public function addAction($id)
    {
        $entity = new Server();
        if( $id > 0 ) {
            $serverType = $this->getDoctrine()
                ->getRepository('DellaertWebappDeploymentBundle:ServerType')
                ->find($id);
            if( $serverType ) {
                $entity->addServerType($serverType);
            }
        }
        $form = $this->createAddEditForm($entity);
        $request = $this->getRequest();
        
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Servers", $this->get("router")->generate("ServerList"));

        if( $request->getMethod() == 'POST' ) {
            $form->handleRequest($request);   
            if( $form->isValid() ) {
                $entity->setEnabled(true);
                $entity->preInsert();

                $keyDir = $this->container->getParameter('dellaert_webapp_deployment.data_dir').'/'.$this->container->getParameter('dellaert_webapp_deployment.sshkey_subdir').$entity->getHost();
                if( $this->createPushSSHKey($entity,$keyDir) ) {
                    $entity->setSshKeyPath($keyDir.'/wdt');
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($entity);
                    $em->flush();
                    $this->get("white_october_breadcrumbs")
                        ->addItem($entity->getHost(), $this->get("router")->generate("ServerViewSlug",array('slug'=>$entity->getSlug())))
                        ->addItem("Save",'');
                    return $this->render('DellaertWebappDeploymentBundle:Server:add.html.twig',array('entity'=>$entity));
                }
            }
        }

        $this->get("white_october_breadcrumbs")->addItem("Add server", '');
        return $this->render('DellaertWebappDeploymentBundle:Server:add.html.twig',array('form'=>$form->createView(),'id'=>$id));
    }
    
    public function editAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:Server')->find($id);
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Servers", $this->get("router")->generate("ServerList"));
        if( $entity ) {
            $this->get("white_october_breadcrumbs")->addItem($entity->getHost(), $this->get("router")->generate("ServerViewSlug",array('slug'=>$entity->getSlug())));
            $form = $this->createAddEditForm($entity);
            $request = $this->getRequest();
            if( $request->getMethod() == 'POST' ) {
                $form->handleRequest($request);   
                if( $form->isValid() ) {
                    $entity->preUpdate();
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($entity);
                    $em->flush();
                    $this->get("white_october_breadcrumbs")->addItem("Save",'');
                    return $this->render('DellaertWebappDeploymentBundle:Server:edit.html.twig',array('entity'=>$entity));
                }
            }
            $this->get("white_october_breadcrumbs")->addItem("Edit",'');
            return $this->render('DellaertWebappDeploymentBundle:Server:edit.html.twig',array('form'=>$form->createView(),'entity'=>$entity));
        }
        $this->get("white_october_breadcrumbs")->addItem("Unkown server", '');
        return $this->render('DellaertWebappDeploymentBundle:Server:edit.html.twig');
    }
    
    public function deleteAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:Server')->find($id);
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Servers", $this->get("router")->generate("ServerList"));
        if( $entity ) {
            $this->get("white_october_breadcrumbs")
                ->addItem($entity->getHost(), $this->get("router")->generate("ServerViewSlug",array('slug'=>$entity->getSlug())))
                ->addItem("Delete",'');
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($entity);
            $em->flush();
            return $this->render('DellaertWebappDeploymentBundle:Server:delete.html.twig',array('entity'=>$entity));
        }
        $this->get("white_october_breadcrumbs")->addItem("Unkown server", '');
        return $this->render('DellaertWebappDeploymentBundle:Server:delete.html.twig');
    }
    
    private function createAddEditForm($entity)
    {
        $fb = $this->createFormBuilder($entity);
        $fb->add('host','text',array('max_length'=>255,'required'=>true,'label'=>'Hostname'));
        $fb->add('ip','text',array('max_length'=>255,'required'=>true,'label'=>'IP'));
        $fb->add('pleskCapable','checkbox',array('required'=>false,'label'=>'Plesk enabled?'));
        $fb->add('pleskUser','text',array('max_length'=>255,'required'=>false,'label'=>'Plesk user'));
        $fb->add('pleskPassword','password',array('max_length'=>255,'required'=>false,'always_empty'=>false,'label'=>'Plesk password'));
        $fb->add('serverTypes','entity',array('class'=>'DellaertWebappDeploymentBundle:ServerType','property'=>'name','expanded'=>true,'multiple'=>true,'label'=>'Server types'));
        if( !$entity->getEnabled() ) {
            $fb->add('wdtPass','password',array('mapped'=>false,'max_length'=>255,'required'=>true,'label'=>'WDT user (wdt) password'));
        }
        return $fb->getForm();
    }

    private function createPushSSHKey($entity,$keyDir)
    {
        
        if( !is_dir($keyDir) ) {
            if(!mkdir($keyDir)) {
                echo("unable to create $keyDir");
                return false;
            }
        }

        exec('ssh-keygen -q -t rsa -b 4096 -N \'\' -f "'.$keyDir.'/wdt');
        if( !is_file($keyDir.'/wdt') ) {
            return false;
        }
        return true;
    }
}
