<?php
namespace Dellaert\WebappDeploymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dellaert\WebappDeploymentBundle\Entity\ApplicationParameterValue;
use Symfony\Component\HttpFoundation\Response;

class ApplicationParameterValueController extends Controller
{
    public function editAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:ApplicationParameterValue')->find($id);
        $this->get("white_october_breadcrumbs")
            ->addItem("Home", $this->get("router")->generate("homepage"))
            ->addItem("Applications", $this->get("router")->generate("ApplicationList"));
        if( $entity ) {
            $this->get("white_october_breadcrumbs")
                ->addItem($entity->getApplication()->getName(), $this->get("router")->generate("ApplicationViewSlug",array('slug'=>$entity->getApplication()->getSlug())))
                ->addItem("Application parameter values", '')
                ->addItem($entity->getApplicationTemplateParameter()->getName(), $this->get("router")->generate("ApplicationTemplateParameterViewId",array('id'=>$entity->getApplicationTemplateParameter()->getId())));
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
                    return $this->render('DellaertWebappDeploymentBundle:ApplicationParameterValue:edit.html.twig',array('entity'=>$entity));
                }
            }
            $this->get("white_october_breadcrumbs")->addItem("Edit",'');
            return $this->render('DellaertWebappDeploymentBundle:ApplicationParameterValue:edit.html.twig',array('form'=>$form->createView(),'entity'=>$entity));
        }
        $this->get("white_october_breadcrumbs")->addItem("Unkown application parameter", '');
        return $this->render('DellaertWebappDeploymentBundle:ApplicationParameterValue:edit.html.twig');
    }
    
    public function createAddEditForm($entity)
    {
        $fb = $this->createFormBuilder($entity);
        if( $entity->getApplicationTemplateParameter()->getIsPassword () ) {
            $fb->add('value','password',array('max_length'=>255,'required'=>true,'label'=>'Value'));
        } else {
            $fb->add('value','text',array('max_length'=>255,'required'=>true,'label'=>'Value'));
        }
        return $fb->getForm();
    }
}
