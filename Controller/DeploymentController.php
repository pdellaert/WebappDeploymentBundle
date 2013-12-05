<?php
namespace Dellaert\WebappDeploymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dellaert\WebappDeploymentBundle\Entity\Deployment;
use Dellaert\WebappDeploymentBundle\Entity\Application;
use Dellaert\WebappDeploymentBundle\Entity\Server;
use Dellaert\WebappDeploymentBundle\Entity\ServerType;
use Symfony\Component\HttpFoundation\Response;

class DeploymentController extends Controller
{
    public function addAction($id)
    {
        $entity = new Deployment();
        $request = $this->getRequest();
        $this->get('white_october_breadcrumbs')
            ->addItem('Home', $this->get('router')->generate('homepage'))
            ->addItem('Applications', $this->get('router')->generate('ApplicationList'));

        if( $id > 0 ) {
            $application = $this->getDoctrine()
                ->getRepository('DellaertWebappDeploymentBundle:Application')
                ->find($id);
            if( $application ) {
                $entity->setApplication($application);
                $this->get('white_october_breadcrumbs')
                    ->addItem($entity->getApplication()->getName(), $this->get('router')->generate('ApplicationViewSlug',array('slug'=>$entity->getApplication()->getSlug())));
            } else {
                $this->get('white_october_breadcrumbs')
                    ->addItem('Unkown application', '')
                    ->addItem('Application Deployment', '');
            
            }
        }

        $form = $this->createAddEditForm($entity);

        if( $request->getMethod() == 'POST' ) {
            $form->handleRequest($request);   
            if( $form->isValid() ) {
                $entity->setEnabled(true);
                $entity->preInsert();
                if( $entity->getPleskCapable() ) {
                    $this->generatePleskValues($entity);
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                $this->get('white_october_breadcrumbs')
                    ->addItem($entity->getHostname(), '')
                    ->addItem('Save','');
                return $this->render('DellaertWebappDeploymentBundle:Deployment:add.html.twig',array('entity'=>$entity));
            }
        }
        $this->get('white_october_breadcrumbs')->addItem('Add deployment', '');
        return $this->render('DellaertWebappDeploymentBundle:Deployment:add.html.twig',array('form'=>$form->createView(),'id'=>$id,'entity'=>$entity));
    }

    public function deployAction($id) {
        $entity = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:Deployment')->find($id);
        $this->get('white_october_breadcrumbs')
            ->addItem('Home', $this->get('router')->generate('homepage'))
            ->addItem('Applications', $this->get('router')->generate('ApplicationList'));
        if( $entity ) {
            $this->get('white_october_breadcrumbs')
                ->addItem($entity->getApplication()->getName(), $this->get('router')->generate('ApplicationViewSlug',array('slug'=>$entity->getApplication()->getSlug())))
                ->addItem($entity->getHostname(), '')
                ->addItem('Deploy','');
            $entity->setDeployed(true);
            if( $entity->getPleskCapable() ) {
                $this->deployPleskDeployment($entity);
            }
            // TODO: ANSIBLE STUFF
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->render('DellaertWebappDeploymentBundle:Deployment:deploy.html.twig',array('entity'=>$entity));
        }
        $this->get("white_october_breadcrumbs")->addItem("Unkown deployment", '');
        return $this->render('DellaertWebappDeploymentBundle:Deployment:deploy.html.twig');
    }

    public function redeployAction($id) {
        $entity = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:Deployment')->find($id);
        $this->get('white_october_breadcrumbs')
            ->addItem('Home', $this->get('router')->generate('homepage'))
            ->addItem('Applications', $this->get('router')->generate('ApplicationList'));
        if( $entity ) {
            $this->get('white_october_breadcrumbs')
                ->addItem($entity->getApplication()->getName(), $this->get('router')->generate('ApplicationViewSlug',array('slug'=>$entity->getApplication()->getSlug())))
                ->addItem($entity->getHostname(), '')
                ->addItem('Redeploy','');
            if( $entity->getPleskCapable() ) {
                $this->redeployPleskDeployment($entity);
            }
            // TODO: ANSIBLE STUFF
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->render('DellaertWebappDeploymentBundle:Deployment:redeploy.html.twig',array('entity'=>$entity));
        }
        $this->get("white_october_breadcrumbs")->addItem("Unkown deployment", '');
        return $this->render('DellaertWebappDeploymentBundle:Deployment:redeploy.html.twig');
    }

    public function undeployAction($id) {
        $entity = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:Deployment')->find($id);
        $this->get('white_october_breadcrumbs')
            ->addItem('Home', $this->get('router')->generate('homepage'))
            ->addItem('Applications', $this->get('router')->generate('ApplicationList'));
        if( $entity ) {
            $this->get('white_october_breadcrumbs')
                ->addItem($entity->getApplication()->getName(), $this->get('router')->generate('ApplicationViewSlug',array('slug'=>$entity->getApplication()->getSlug())))
                ->addItem($entity->getHostname(), '')
                ->addItem('Undeploy','');
            if( $entity->getPleskCapable() ) {
                $this->redeployPleskDeployment($entity);
            }
            // TODO: ANSIBLE STUFF
            $entity->setDeployed(false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->render('DellaertWebappDeploymentBundle:Deployment:undeploy.html.twig',array('entity'=>$entity));
        }
        $this->get("white_october_breadcrumbs")->addItem("Unkown deployment", '');
        return $this->render('DellaertWebappDeploymentBundle:Deployment:undeploy.html.twig');
    }

    public function deleteAction($id) {
        $entity = $this->getDoctrine()->getRepository('DellaertWebappDeploymentBundle:Deployment')->find($id);
        $this->get('white_october_breadcrumbs')
            ->addItem('Home', $this->get('router')->generate('homepage'))
            ->addItem('Applications', $this->get('router')->generate('ApplicationList'));
        if( $entity ) {
            $this->get('white_october_breadcrumbs')
                ->addItem($entity->getApplication()->getName(), $this->get('router')->generate('ApplicationViewSlug',array('slug'=>$entity->getApplication()->getSlug())))
                ->addItem($entity->getHostname(), '')
                ->addItem('Delete','');
            // TODO: ANSIBLE STUFF
            if( $entity->getPleskCapable() ) {
                $this->deletePleskDeployment($entity);
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();
            return $this->render('DellaertWebappDeploymentBundle:Deployment:delete.html.twig',array('entity'=>$entity));
        }
        $this->get("white_october_breadcrumbs")->addItem("Unkown deployment", '');
        return $this->render('DellaertWebappDeploymentBundle:Deployment:delete.html.twig');
    }

    private function deployPleskDeployment($entity) {
        // TODO: Add logic
        // Step 1: create subscription
        $subscriptionHandle = \Dellaert\PleskRemoteControlBundle\Utility\PleskAPIUtility::createSubscription($entity->getServer()->getHost(),$entity->getServer()->getPleskUser(),$entity->getServer()->getPleskPassword(),$entity->getHostname(),$entity->getServer()->getIp(),$entity->getPleskAdminUserName(),$entity->getPleskAdminUserPass());
        $subscriptionResultXML = simplexml_load_string($subscriptionHandle['result']);
        if( !isset($subscriptionResultXML->system->errcode) && !isset($subscriptionResultXML->webspace->add->result->errcode) && $subscriptionResultXML->webspace->add->result->status == 'ok' ) {
            $entity->setPleskSubscriptionId($subscriptionResultXML->webspace->add->result->id);
        }
        // Step 2: create admin user
        // Step 3: create database
        // Step 4: create database user
        return $entity;
    }

    private function redeployPleskDeployment($entity) {
        // TODO: Add logic
        return $entity;
    }

    private function deletePleskDeployment($entity) {
        // TODO: Add logic
        return $entity;
    }

    private function generatePleskValues($entity)
    {
        $entity->setPleskAdminUserName(substr($entity->getServerType()->getCode().'-'.$entity->getApplication()->getSlug(),0,16));
        $entity->setPleskAdminUserPass(substr(str_replace('=',md5(uniqid(rand(),true)),base64_encode(md5(uniqid(rand(),true)))),0,20));
        $entity->setPleskDBName(substr($entity->getServerType()->getCode().'_'.$entity->getApplication()->getSlug(),0,32));
        $entity->setPleskDBUserName(substr($entity->getServerType()->getCode().'_'.$entity->getApplication()->getSlug(),0,32));
        $entity->setPleskDBUserPass(substr(str_replace('=',md5(uniqid(rand(),true)),base64_encode(md5(uniqid(rand(),true)))),0,20));
        return $entity;
    }
    
    private function createAddEditForm($entity)
    {
        $fb = $this->createFormBuilder($entity);
        $fb->add('hostname','text',array('max_length'=>255,'required'=>true,'label'=>'Hostname'));
        $fb->add('pleskCapable','checkbox',array('required'=>false,'label'=>'Plesk enabled?'));
        $fb->add('application','entity',array('class'=>'DellaertWebappDeploymentBundle:Application','property'=>'name','expanded'=>false,'multiple'=>false,'label'=>'Application'));
        $fb->add('server','entity',array('class'=>'DellaertWebappDeploymentBundle:Server','property'=>'host','expanded'=>false,'multiple'=>false,'label'=>'Server'));
        $fb->add('serverType','entity',array('class'=>'DellaertWebappDeploymentBundle:ServerType','property'=>'name','expanded'=>false,'multiple'=>false,'label'=>'Type'));
        return $fb->getForm();
    }
}
