<?php
namespace Dellaert\WebappDeploymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dellaert\WebappDeploymentBundle\Entity\Deployment;
use Dellaert\WebappDeploymentBundle\Entity\Application;
use Dellaert\WebappDeploymentBundle\Entity\Server;
use Dellaert\WebappDeploymentBundle\Entity\ServerType;
use Dellaert\PleskRemoteControlBundle\Utility\PleskAPIUtility;
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
            $pleskResult = array('succes'=>true);
            $ansibleResult = array('succes'=>true);
            if( $entity->getPleskCapable() ) {
                $pleskResult = $this->deployPleskDeployment($entity);
            }
            // TODO: ANSIBLE STUFF
            if( $pleskResult['succes'] && $ansibleResult['succes'] ) {
                $entity->setDeployed(true);
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
            }
            return $this->render('DellaertWebappDeploymentBundle:Deployment:deploy.html.twig',array('entity'=>$entity,'pleskResult'=>$pleskResult,'ansibleResult'=>$ansibleResult));
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
            $pleskResult = array('succes'=>true);
            $ansibleResult = array('succes'=>true);
            if( $entity->getPleskCapable() ) {
                $pleskResult = $this->redeployPleskDeployment($entity);
            }
            // TODO: ANSIBLE STUFF
            if( $pleskResult['succes'] && $ansibleResult['succes'] ) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
            }
            return $this->render('DellaertWebappDeploymentBundle:Deployment:redeploy.html.twig',array('entity'=>$entity,'pleskResult'=>$pleskResult,'ansibleResult'=>$ansibleResult));
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
            $pleskResult = array('succes'=>true);
            $ansibleResult = array('succes'=>true);
            if( $entity->getPleskCapable() ) {
                $pleskResult = $this->undeployPleskDeployment($entity);
            }
            // TODO: ANSIBLE STUFF
            if( $pleskResult['succes'] && $ansibleResult['succes'] ) {
                $entity->setDeployed(false);
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
            }
            return $this->render('DellaertWebappDeploymentBundle:Deployment:undeploy.html.twig',array('entity'=>$entity,'pleskResult'=>$pleskResult,'ansibleResult'=>$ansibleResult));
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
            $pleskResult = array('succes'=>true);
            $ansibleResult = array('succes'=>true);
            // TODO: ANSIBLE STUFF
            if( $entity->getPleskCapable() ) {
                $pleskResult = $this->deletePleskDeployment($entity);
            }
            if( $pleskResult['succes'] && $ansibleResult['succes'] ) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($entity);
                $em->flush();
            }
            return $this->render('DellaertWebappDeploymentBundle:Deployment:delete.html.twig',array('entity'=>$entity,'pleskResult'=>$pleskResult,'ansibleResult'=>$ansibleResult));
        }
        $this->get("white_october_breadcrumbs")->addItem("Unkown deployment", '');
        return $this->render('DellaertWebappDeploymentBundle:Deployment:delete.html.twig');
    }

    private function deployPleskDeployment($entity) {
        // Step 1: create subscription
        $subscriptionHandle = PleskAPIUtility::createSubscription(
            $entity->getServer()->getHost(),
            $entity->getServer()->getPleskUser(),
            $entity->getServer()->getPleskPassword(),
            $entity->getHostname(),
            $entity->getServer()->getIp(),
            $entity->getPleskAdminUserName(),
            $entity->getPleskAdminUserPass());
        $subscriptionResultXML = simplexml_load_string($subscriptionHandle['result']);
        if( !isset($subscriptionResultXML->system->errcode) && !isset($subscriptionResultXML->webspace->add->result->errcode) && $subscriptionResultXML->webspace->add->result->status == 'ok' ) {
            $entity->setPleskSubscriptionId($subscriptionResultXML->webspace->add->result->id);

            // Step 2: create admin user
            $adminHandle = PleskAPIUtility::createUser(
                $entity->getServer()->getHost(),
                $entity->getServer()->getPleskUser(),
                $entity->getServer()->getPleskPassword(),
                $entity->getPleskSubscriptionId(),
                $entity->getPleskAdminUserName(),
                $entity->getPleskAdminUserPass(),
                $entity->getPleskAdminUserName());
            $adminResultXML = simplexml_load_string($adminHandle['result']);
            if( !isset($adminResultXML->system->errcode) &&  !isset($adminResultXML->user->add->result->errcode) && $adminResultXML->user->add->result->status == 'ok' ) {
                $entity->setPleskAdminUserId($adminResultXML->user->add->result->id);

                // Step 3: create database
                $dbHandle = PleskAPIUtility::createDatabase(
                    $entity->getServer()->getHost(),
                    $entity->getServer()->getPleskUser(),
                    $entity->getServer()->getPleskPassword(),
                    $entity->getPleskSubscriptionId(),
                    $entity->getPleskDBName(),
                    $entity->getApplication()->getApplicationTemplate()->getDatabaseType()->getCode(),
                    $entity->getApplication()->getApplicationTemplate()->getDatabaseType()->getPleskDBId());
                $dbResultXML = simplexml_load_string($dbHandle['result']);
                if( !isset($dbResultXML->system->errcode) &&  !isset($dbResultXML->database->{'add-db'}->result->errcode) && $dbResultXML->database->{'add-db'}->result->status == 'ok' ) {
                    $entity->setPleskDBId($dbResultXML->database->{'add-db'}->result->id);

                    // Step 4: create database user
                    $dbUserHandle = PleskAPIUtility::createDatabaseUser(
                        $entity->getServer()->getHost(),
                        $entity->getServer()->getPleskUser(),
                        $entity->getServer()->getPleskPassword(),
                        $entity->getPleskDBId(),
                        $entity->getPleskDBUserName(),
                        $entity->getPleskDBUserPass());
                    $dbUserResultXML = simplexml_load_string($dbUserHandle['result']);
                    if( !isset($dbUserResultXML->system->errcode) &&  !isset($dbUserResultXML->database->{'add-db-user'}->result->errcode) && $dbUserResultXML->database->{'add-db-user'}->result->status == 'ok' ) {
                        $entity->setPleskDBUserId($dbUserResultXML->database->{'add-db-user'}->result->id);
                        return array('succes'=>true);
                    } elseif( isset($dbUserResultXML->system->errcode) ) {
                        return array('succes'=>false,'error'=>'Unable to create Plesk database user, error code: '.$dbUserResultXML->system->errcode);
                    } else {
                        return array('succes'=>false,'error'=>'Unable to create Plesk database user, error code: '.$dbUserResultXML->database->{'add-db-user'}->result->errcode);
                    }                
                } elseif( isset($dbResultXML->system->errcode) ) {
                    return array('succes'=>false,'error'=>'Unable to create Plesk database, error code: '.$dbResultXML->system->errcode);
                } else {
                    return array('succes'=>false,'error'=>'Unable to create Plesk database, error code: '.$dbResultXML->database->{'add-db'}->result->errcode);
                }
            } elseif( isset($adminResultXML->system->errcode) ) {
                return array('succes'=>false,'error'=>'Unable to create Plesk admin user, error code: '.$adminResultXML->system->errcode);
            } else {
                return array('succes'=>false,'error'=>'Unable to create Plesk admin user, error code: '.$adminResultXML->user->add->result->errcode);
            }
        } elseif( isset($subscriptionResultXML->system->errcode) ) {
            return array('succes'=>false,'error'=>'Unable to create Plesk subscription/webspace, error code: '.$subscriptionResultXML->system->errcode);
        } else {
            return array('succes'=>false,'error'=>'Unable to create Plesk subscription/webspace, error code: '.$subscriptionResultXML->webspace->add->result->errcode);
        }
    }

    private function redeployPleskDeployment($entity) {
        // TODO: Add logic
        return array('succes'=>true);
    }

    private function undeployPleskDeployment($entity) {
        // TODO: Add logic
        return array('succes'=>true);
    }

    private function deletePleskDeployment($entity) {
        // TODO: Add logic
        return array('succes'=>true);
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
