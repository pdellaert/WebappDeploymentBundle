<?php
namespace Dellaert\WebappDeploymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dellaert\WebappDeploymentBundle\Entity\Server;
use Dellaert\WebappDeploymentBundle\Entity\ServerType;
use Dellaert\WebappDeploymentBundle\Controller\ServerController;
use Symfony\Component\HttpFoundation\Response;

class ServerTypeController extends Controller
{
    public function listAction()
    {
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Server types", $this->get("router")->generate("ServerTypeList"));
        return $this->render('DellaertWebappDeploymentBundle:ServerType:list.html.twig');
    }
    
    public function listDataAction()
    {
        $request = $this->getRequest();
        
        $page = 1;
        if( $request->request->get('page') != null && $request->request->get('page') != '' ) {
            $page = $request->request->get('page');
        }
        
        $sortname = 'name';
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
        
        $repository = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:ServerType');
        $qb = $repository->createQueryBuilder('c');
        if( $searchquery != '' && $searchtype != '' ) {
            $qb->add('where',$qb->expr()->like('c.'.$searchtype, $qb->expr()->literal('%'.$searchquery.'%')));
        }
        $qb->orderBy('c.'.$sortname,$sortorder);
        $qb->setFirstResult($pageStart);
        $qb->setMaxResults($rp);
        $query = $qb->getQuery();
        $results = $query->getResult();
        
        $em = $this->getDoctrine()->getManager();
        $qstring = 'SELECT COUNT(c.id) FROM DellaertWebappDeploymentBundle:ServerType c';
        if( $searchquery != '' && $searchtype != '' ) {
            $qstring .= ' where '.$qb->expr()->like('c.'.$searchtype, $qb->expr()->literal('%'.$searchquery.'%'));
        }
        $query = $em->createQuery($qstring);
        $total = $query->getSingleScalarResult();
        
        $data['page'] = $page;
        $data['total'] = $total;
        $data['rows'] = array();
        foreach($results as $entity) {
            $data['rows'][] = array(
                'id' => $entity->getSlug(),
                'cell' => array($entity->getName(),$entity->getCode())
            );
        }
        
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
    
    public function viewAction($slug)
    {
        $repository = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:ServerType');
        $entity = $repository->findOneBySlug($slug);
        
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Server types", $this->get("router")->generate("ServerTypeList"));
        if( $entity ) {
            $this->get("white_october_breadcrumbs")->addItem($entity->getName(), $this->get("router")->generate("ServerTypeViewSlug",array('slug'=>$slug)));
        } else {
            $this->get("white_october_breadcrumbs")->addItem("Unkown server type", '');
        }
        
        return $this->render('DellaertWebappDeploymentBundle:ServerType:view.html.twig',array('entity'=>$entity));
    }
    
    public function addAction()
    {
        $entity = new ServerType();
        $form = $this->createAddEditForm($entity);
        $request = $this->getRequest();
        
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Server types", $this->get("router")->generate("ServerTypeList"));
        
        if( $request->getMethod() == 'POST' ) {
            $form->handleRequest($request);   
            if( $form->isValid() ) {
                $entity->setEnabled(true);
                $entity->preInsert();
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                $this->get("white_october_breadcrumbs")
                    ->addItem($entity->getName(), $this->get("router")->generate("ServerTypeViewSlug",array('slug'=>$entity->getSlug())))
                    ->addItem("Save",'');
                return $this->render('DellaertWebappDeploymentBundle:ServerType:add.html.twig',array('entity'=>$entity));
            }
        }
        $this->get("white_october_breadcrumbs")->addItem("Add server type", '');
        return $this->render('DellaertWebappDeploymentBundle:ServerType:add.html.twig',array('form'=>$form->createView()));
    }
    
    public function editAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:ServerType')->find($id);
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Server types", $this->get("router")->generate("ServerTypeList"));
        if( $entity ) {
            $this->get("white_october_breadcrumbs")->addItem($entity->getName(), $this->get("router")->generate("ServerTypeViewSlug",array('slug'=>$entity->getSlug())));
            $form = $this->createAddEditForm($entity);
            $request = $this->getRequest();
            if( $request->getMethod() == 'POST' ) {
                $form->handleRequest($request);   
                if( $form->isValid() ) {
                    $entity->preUpdate();
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($entity);
                    $em->flush();
                    $this->get("white_october_breadcrumbs")->addItem("Save",'');
                    return $this->render('DellaertWebappDeploymentBundle:ServerType:edit.html.twig',array('entity'=>$entity));
                }
            }
            $this->get("white_october_breadcrumbs")->addItem("Edit",'');
            return $this->render('DellaertWebappDeploymentBundle:ServerType:edit.html.twig',array('form'=>$form->createView(),'entity'=>$entity));
        }
        $this->get("white_october_breadcrumbs")->addItem("Unkown server type", '');
        return $this->render('DellaertWebappDeploymentBundle:ServerType:edit.html.twig');
    }
    
    public function deleteAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:ServerType')->find($id);
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Server types", $this->get("router")->generate("ServerTypeList"));
        if( $entity ) {
            $this->get("white_october_breadcrumbs")
                ->addItem($entity->getName(), $this->get("router")->generate("ServerTypeViewSlug",array('slug'=>$entity->getSlug())))
                ->addItem("Delete",'');
            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();
            return $this->render('DellaertWebappDeploymentBundle:ServerType:delete.html.twig',array('entity'=>$entity));
        }
        $this->get("white_october_breadcrumbs")->addItem("Unkown server type", '');
        return $this->render('DellaertWebappDeploymentBundle:ServerType:delete.html.twig');
    }
    
    public function createAddEditForm($entity)
    {
        $fb = $this->createFormBuilder($entity);
        $fb->add('name','text',array('max_length'=>255,'required'=>true,'label'=>'Name'));
        $fb->add('code','text',array('max_length'=>255,'required'=>true,'label'=>'Shortcode'));
        return $fb->getForm();
    }
}
