<?php
namespace Dellaert\WebappDeploymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dellaert\WebappDeploymentBundle\Entity\APIKey;
use Symfony\Component\HttpFoundation\Response;

class APIKeyController extends Controller
{
    public function listAction()
    {
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("API Keys", $this->get("router")->generate("APIKeyList"));
        return $this->render('DellaertWebappDeploymentBundle:APIKey:list.html.twig');
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
        
        $repository = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:APIKey');
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
        $qstring = 'SELECT COUNT(c.id) FROM DellaertWebappDeploymentBundle:APIKey c';
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
                'cell' => array($entity->getName(), $entity->getApikey())
            );
        }
        
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
    
    public function viewAction($slug)
    {
        $repository = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:APIKey');
        $entity = $repository->findOneBySlug($slug);
        
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("API Keys", $this->get("router")->generate("APIKeyList"));
        if( $entity ) {
            $this->get("white_october_breadcrumbs")->addItem($entity->getName(), $this->get("router")->generate("APIKeyViewSlug",array('slug'=>$slug)));
        } else {
            $this->get("white_october_breadcrumbs")->addItem("Unkown API key", '');
        }
        
        return $this->render('DellaertWebappDeploymentBundle:APIKey:view.html.twig',array('entity'=>$entity));
    }
    
    // TODO: NEED TO GENERATE KEY!
    public function addAction()
    {
        $entity = new APIKey();
        $entity->setApikey(substr(str_replace('=',md5(uniqid(rand(),true)),base64_encode(md5(uniqid(rand(),true)).md5(uniqid(rand(),true)))),0,128));
        $form = $this->createAddEditForm($entity);
        $request = $this->getRequest();
        
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("API Keys", $this->get("router")->generate("APIKeyList"));
        
        if( $request->getMethod() == 'POST' ) {
            $form->bindRequest($request);   
            if( $form->isValid() ) {
                $entity->setEnabled(true);
                $entity->preInsert();
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($entity);
                $em->flush();
                $this->get("white_october_breadcrumbs")
                    ->addItem($entity->getName(), $this->get("router")->generate("APIKeyViewSlug",array('slug'=>$entity->getSlug())))
                    ->addItem("Save",'');
                return $this->render('DellaertWebappDeploymentBundle:APIKey:add.html.twig',array('entity'=>$entity));
            }
        }
        $this->get("white_october_breadcrumbs")->addItem("Add API key", '');
        return $this->render('DellaertWebappDeploymentBundle:APIKey:add.html.twig',array('form'=>$form->createView()));
    }
    
    public function editAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:APIKey')->find($id);
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("API Keys", $this->get("router")->generate("APIKeyList"));
        if( $entity ) {
            $this->get("white_october_breadcrumbs")->addItem($entity->getName(), $this->get("router")->generate("APIKeyViewSlug",array('slug'=>$entity->getSlug())));
            $form = $this->createAddEditForm($entity);
            $request = $this->getRequest();
            if( $request->getMethod() == 'POST' ) {
                $form->bindRequest($request);   
                if( $form->isValid() ) {
                    $entity->preUpdate();
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($entity);
                    $em->flush();
                    $this->get("white_october_breadcrumbs")->addItem("Save",'');
                    return $this->render('DellaertWebappDeploymentBundle:APIKey:edit.html.twig',array('entity'=>$entity));
                }
            }
            $this->get("white_october_breadcrumbs")->addItem("Edit",'');
            return $this->render('DellaertWebappDeploymentBundle:APIKey:edit.html.twig',array('form'=>$form->createView(),'entity'=>$entity));
        }
        $this->get("white_october_breadcrumbs")->addItem("Unkown API Key", '');
        return $this->render('DellaertWebappDeploymentBundle:APIKey:edit.html.twig');
    }
    
    public function deleteAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:APIKey')->find($id);
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("API Keys", $this->get("router")->generate("APIKeyList"));
        if( $entity ) {
            $this->get("white_october_breadcrumbs")
                ->addItem($entity->getName(), $this->get("router")->generate("APIKeyViewSlug",array('slug'=>$entity->getSlug())))
                ->addItem("Delete",'');
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($entity);
            $em->flush();
            return $this->render('DellaertWebappDeploymentBundle:APIKey:delete.html.twig',array('entity'=>$entity));
        }
        $this->get("white_october_breadcrumbs")->addItem("Unkown API Key", '');
        return $this->render('DellaertWebappDeploymentBundle:APIKey:delete.html.twig');
    }
    
    public function createAddEditForm($entity)
    {
        $fb = $this->createFormBuilder($entity);
        $fb->add('name','text',array('max_length'=>255,'required'=>true,'label'=>'Name'));
        $fb->add('apikey','text',array('max_length'=>128,'width'=300,'required'=>true,'label'=>'API Key','read_only'=>true));
        return $fb->getForm();
    }
}
