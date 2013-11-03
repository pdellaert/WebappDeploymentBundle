<?php
namespace Dellaert\WebappDeploymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dellaert\WebappDeploymentBundle\Entity\Application;
use Dellaert\WebappDeploymentBundle\Entity\ApplicationParameterValue;
use Dellaert\WebappDeploymentBundle\Entity\ApplicationTemplate;
use Dellaert\WebappDeploymentBundle\Entity\ApplicationTemplateParameter;
use Symfony\Component\HttpFoundation\Response;

class ApplicationController extends Controller
{
    public function listAction()
    {
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Applications", $this->get("router")->generate("ApplicationList"));
        return $this->render('DellaertWebappDeploymentBundle:Application:list.html.twig');
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
        
        $repository = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:Application');
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
        $qstring = 'SELECT COUNT(c.id) FROM DellaertWebappDeploymentBundle:Application c';
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
            $data['rows'][] = array(
                'id' => $entity->getSlug(),
                'cell' => array($entity->getName(),$entity->getOrganisation(),$entity->getApplicationTemplate()->getName(),$pleskCapable)
            );
        }
        
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
    
    public function viewAction($slug)
    {
        $repository = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:Application');
        $entity = $repository->findOneBySlug($slug);
        
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Applications", $this->get("router")->generate("ApplicationList"));
        if( $entity ) {
            $this->get("white_october_breadcrumbs")->addItem($entity->getName(), $this->get("router")->generate("ApplicationViewSlug",array('slug'=>$slug)));
        } else {
            $this->get("white_october_breadcrumbs")->addItem("Unkown application", '');
        }
        
        return $this->render('DellaertWebappDeploymentBundle:Application:view.html.twig',array('entity'=>$entity));
    }
    
    public function addAction($id)
    {
        $entity = new Application();
        if( $id > 0 ) {
            $applicationTemplate = $this->getDoctrine()
                ->getRepository('DellaertWebappDeploymentBundle:ApplicationTemplate')
                ->find($id);
            if( $applicationTemplate ) {
                $entity->setApplicationTemplate($applicationTemplate);
            }
        }
        $form = $this->createAddEditForm($entity);
        $request = $this->getRequest();
        
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Applications", $this->get("router")->generate("ApplicationList"));

        if( $request->getMethod() == 'POST' ) {
            $form->handleRequest($request);   
            if( $form->isValid() ) {
                $entity->setEnabled(true);
                $entity->preInsert();
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($entity);
                $em->flush();
                // Saving Parameters
                $this->updateApplicationParameterValues($entity);
                $this->get("white_october_breadcrumbs")
                    ->addItem($entity->getName(), $this->get("router")->generate("ApplicationViewSlug",array('slug'=>$entity->getSlug())))
                    ->addItem("Save",'');
                return $this->render('DellaertWebappDeploymentBundle:Application:add.html.twig',array('entity'=>$entity));
            }
        }

        $this->get("white_october_breadcrumbs")->addItem("Add application", '');
        return $this->render('DellaertWebappDeploymentBundle:Application:add.html.twig',array('form'=>$form->createView(),'id'=>$id));
    }
    
    public function editAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:Application')->find($id);
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Applications", $this->get("router")->generate("ApplicationList"));
        if( $entity ) {
            $this->get("white_october_breadcrumbs")->addItem($entity->getName(), $this->get("router")->generate("ApplicationViewSlug",array('slug'=>$entity->getSlug())));
            $form = $this->createAddEditForm($entity);
            $request = $this->getRequest();
            if( $request->getMethod() == 'POST' ) {
                $form->handleRequest($request);   
                if( $form->isValid() ) {
                    $entity->preUpdate();
                    // Saving Entity
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($entity);
                    $em->flush();
                    // Saving Parameters
                    $this->updateApplicationParameterValues($entity);
                    $this->get("white_october_breadcrumbs")->addItem("Save",'');
                    return $this->render('DellaertWebappDeploymentBundle:Application:edit.html.twig',array('entity'=>$entity));
                }
            }
            $this->get("white_october_breadcrumbs")->addItem("Edit",'');
            return $this->render('DellaertWebappDeploymentBundle:Application:edit.html.twig',array('form'=>$form->createView(),'entity'=>$entity));
        }
        $this->get("white_october_breadcrumbs")->addItem("Unkown application", '');
        return $this->render('DellaertWebappDeploymentBundle:Application:edit.html.twig');
    }
    
    public function deleteAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:Application')->find($id);
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Applications", $this->get("router")->generate("ApplicationList"));
        if( $entity ) {
            $this->get("white_october_breadcrumbs")
                ->addItem($entity->getName(), $this->get("router")->generate("ApplicationViewSlug",array('slug'=>$entity->getSlug())))
                ->addItem("Delete",'');
            $em = $this->getDoctrine()->getEntityManager();
            foreach( $entity->getApplicationParameterValues() as $applicationParameterValue ) {
                $em->remove($applicationParameterValue);
            }
            $em->remove($entity);
            $em->flush();
            return $this->render('DellaertWebappDeploymentBundle:Application:delete.html.twig',array('entity'=>$entity));
        }
        $this->get("white_october_breadcrumbs")->addItem("Unkown application", '');
        return $this->render('DellaertWebappDeploymentBundle:Application:delete.html.twig');
    }
    
    private function createAddEditForm($entity)
    {
        $fb = $this->createFormBuilder($entity);
        $fb->add('name','text',array('max_length'=>255,'required'=>true,'label'=>'Name'));
        $fb->add('organisation','text',array('max_length'=>255,'required'=>true,'label'=>'Organisation'));
        $fb->add('pleskCapable','checkbox',array('required'=>false,'label'=>'Plesk enabled?'));
        $fb->add('applicationTemplate','entity',array('class'=>'DellaertWebappDeploymentBundle:ApplicationTemplate','property'=>'name','expanded'=>false,'multiple'=>false,'label'=>'Application template'));
        return $fb->getForm();
    }

    private function updateApplicationParameterValues($entity) {
        $em = $this->getDoctrine()->getEntityManager();
        // Creating every template parameter that does not exist for the enitity yet
        foreach( $entity->getApplicationTemplate()->getApplicationTemplateParameters() as $applicationTemplateParameter ) {
            $found = false;
            foreach( $entity->getApplicationParameterValues() as $applicationParameterValue ) {
                if( $applicationParameterValue->getApplicationTemplateParameter()->getId() == $applicationTemplateParameter->getId() ) {
                    $found = true;
                    break;
                }
            }

            if( !$found ) {
                $applicationParameterValue = new ApplicationParameterValue();
                $applicationParameterValue->setApplication($entity);
                $applicationParameterValue->setApplicationTemplateParameter($applicationTemplateParameter);
                $applicationParameterValue->setEnabled(true);
                $applicationParameterValue->preInsert();
                $applicationParameterValue->setValue('');
                $em->persist($applicationParameterValue);
                $em->flush();
            }
        }

        // Deleting every parameter value for every template parameter that does not exist in the template
        foreach( $entity->getApplicationParameterValues() as $applicationParameterValue ) {
            $found = false;
            foreach( $entity->getApplicationTemplate()->getApplicationTemplateParameters() as $applicationTemplateParameter ) {
                if( $applicationParameterValue->getApplicationTemplateParameter()->getId() == $applicationTemplateParameter->getId() ) {
                    $found = true;
                    break;
                }
            }

            if( !$found ) {
                $em->remove($applicationParameterValue);
                $em->flush();
            }
        }

    }
}
