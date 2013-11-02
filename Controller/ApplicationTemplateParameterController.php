<?php
namespace Dellaert\WebappDeploymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dellaert\WebappDeploymentBundle\Entity\ApplicationTemplateParameter;
use Symfony\Component\HttpFoundation\Response;

class ApplicationTemplateParameterController extends Controller
{    
    public function viewAction($id)
    {
        $repository = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:ApplicationTemplateParameter');
        $entity = $repository->findOneById($id);
        
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Application templates", $this->get("router")->generate("ApplicationTemplateList"));
        if( $entity ) {
            $this->get("white_october_breadcrumbs")
                ->addItem($entity->getApplicationTemplate()->getName(), $this->get("router")->generate("ApplicationTemplateViewSlug",array('slug',$entity->getApplicationTemplate()->getSlug())))
                ->addItem("Application template parameters", '')
                ->addItem($entity->getName(), $this->get("router")->generate("ApplicationTemplateParameterViewId",array('Id'=>$id)));
        } else {
            $this->get("white_october_breadcrumbs")
                ->addItem("Unkown application template", '')
                ->addItem("Application template parameters", '')
                ->addItem("Unkown application template parameter", '');
        }
        
        return $this->render('DellaertWebappDeploymentBundle:ApplicationTemplateParameter:view.html.twig',array('entity'=>$entity));
    }
    
    public function addAction($id)
    {
        $entity = new ApplicationTemplateParameter();
        $request = $this->getRequest();
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Application templates", $this->get("router")->generate("ApplicationTemplateList"));

        if( $id > 0 ) {
            $applicationTemplate = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:ApplicationTemplateParameter')->find($id);
            if( $applicationTemplate ) {
                $entity->setApplicationTemplate($applicationTemplate);
                $this->get('session')->set('return_url',$this->get('router')->generate('ApplicationTemplateViewSlug', array('slug'=>$applicationTemplate->getSlug())));
                $this->get("white_october_breadcrumbs")
                    ->addItem($entity->getApplicationTemplate()->getName(), $this->get("router")->generate("ApplicationTemplateViewSlug",array('slug',$entity->getApplicationTemplate()->getSlug())))
            } else {
            $this->get("white_october_breadcrumbs")
                ->addItem("Unkown application template", '')
                ->addItem("Application template parameters", '');
            }
        } elseif( $request->getMethod() != 'POST' ) {
            $this->get('session')->remove('return_url');
        }
        if( $this->get('session')->get('return_url') == null || $this->get('session')->get('return_url') == '' ) {
            $this->get('session')->set('return_url',$this->get('router')->generate('ApplicationTemplateList'));
        }

        $form = $this->createAddEditForm($entity);
        
        if( $request->getMethod() == 'POST' ) {
            $form->handleRequest($request);   
            if( $form->isValid() ) {
                $entity->setEnabled(true);
                $entity->preInsert();
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($entity);
                $em->flush();
                $this->get("white_october_breadcrumbs")
                    ->addItem($entity->getName(), $this->get("router")->generate("ApplicationTemplateParameterViewId",array('Id'=>$entity->getId())))
                    ->addItem("Save",'');
                return $this->render('DellaertWebappDeploymentBundle:ApplicationTemplateParameter:add.html.twig',array('entity'=>$entity));
            }
        }
        $this->get("white_october_breadcrumbs")->addItem("Add application template parameter", '');
        return $this->render('DellaertWebappDeploymentBundle:ApplicationTemplateParameter:add.html.twig',array('form'=>$form->createView(),'id'=>$id));
    }
    
    public function editAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:ApplicationTemplateParameter')->find($id);
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Application templates", $this->get("router")->generate("ApplicationTemplateList"));
        if( $entity ) {
            $this->get("white_october_breadcrumbs")
                ->addItem($entity->getApplicationTemplate()->getName(), $this->get("router")->generate("ApplicationTemplateViewSlug",array('slug',$entity->getApplicationTemplate()->getSlug())))
                ->addItem("Application template parameters", '')
                ->addItem($entity->getName(), $this->get("router")->generate("ApplicationTemplateParameterViewId",array('Id'=>$id)));
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
                    return $this->render('DellaertWebappDeploymentBundle:ApplicationTemplateParameter:edit.html.twig',array('entity'=>$entity));
                }
            }
            $this->get("white_october_breadcrumbs")->addItem("Edit",'');
            return $this->render('DellaertWebappDeploymentBundle:ApplicationTemplateParameter:edit.html.twig',array('form'=>$form->createView(),'entity'=>$entity));
        }
        $this->get("white_october_breadcrumbs")->addItem("Unkown application template parameter", '');
        return $this->render('DellaertWebappDeploymentBundle:ApplicationTemplateParameter:edit.html.twig');
    }
    
    public function deleteAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:ApplicationTemplateParameter')->find($id);
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Application templates", $this->get("router")->generate("ApplicationTemplateList"));
        if( $entity ) {
            $this->get("white_october_breadcrumbs")
                ->addItem($entity->getApplicationTemplate()->getName(), $this->get("router")->generate("ApplicationTemplateViewSlug",array('slug',$entity->getApplicationTemplate()->getSlug())))
                ->addItem("Application template parameters", '')
                ->addItem($entity->getName(), $this->get("router")->generate("ApplicationTemplateParameterViewId",array('Id'=>$id)))
                ->addItem("Delete",'');
            $this->get('session')->set('return_url',$this->get('router')->generate('ApplicationTemplateViewSlug', array('slug'=>$entity->getApplicationTemplate()->getSlug())));
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($entity);
            $em->flush();
            return $this->render('DellaertWebappDeploymentBundle:ApplicationTemplateParameter:delete.html.twig',array('entity'=>$entity));
        }
        $this->get("white_october_breadcrumbs")->addItem("Unkown application template", '');
        return $this->render('DellaertWebappDeploymentBundle:ApplicationTemplateParameter:delete.html.twig');
    }
    
    public function createAddEditForm($entity)
    {
        $fb = $this->createFormBuilder($entity);
        $fb->add('name','text',array('max_length'=>255,'required'=>true,'label'=>'Name'));
        $fb->add('isPassword','checkbox',array('required'=>false,'label'=>'Password'));
        $fb->add('applicationTemplate','entity',array('class'=>'DellaertWebappDeploymentBundle:ApplicationTemplate','property'=>'name','expanded'=>false,'multiple'=>false,'label'=>'Application Template'));
        return $fb->getForm();
    }
}
