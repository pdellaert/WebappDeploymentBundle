<?php
namespace Dellaert\WebappDeploymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dellaert\WebappDeploymentBundle\Entity\Deployment;
use Dellaert\WebappDeploymentBundle\Entity\Application;
use Dellaert\WebappDeploymentBundle\Entity\Server;
use Dellaert\WebappDeploymentBundle\Entity\DeploymentType;
use Dellaert\PleskRemoteControlBundle\Utility\PleskAPIUtility;
use Dellaert\WebappDeploymentBundle\Utility\AnsibleUtility;
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
            // Plesk stuff
            if( $entity->getPleskCapable() ) {
                $pleskResult = $this->deployPleskDeployment($entity);
            }
            // Ansible stuff
            if( $pleskResult['succes'] ) {
                $arguments = 'action=deploy vhost='.$entity->getHostname();
                if( $entity->getPleskCapable() ) {
                    $arguments .= ' admin_user='.$entity->getPleskAdminUserName()
                        .' admin_pass='.$entity->getPleskAdminUserPass()
                        .' db_name='.$entity->getPleskDBName()
                        .' db_username='.$entity->getPleskDBUserName()
                        .' db_password='.$entity->getPleskDBUserPass()
                        .' db_host='.$entity->getPleskDBHost()
                        .' git_branch='.$entity->getDeploymentType()->getGitBranch()
                }
                foreach( $entity->getApplication()->getApplicationParameterValues() as $appParameter ) {
                    if( $appParameter->getValue() != '' ) {
                        $arguments .= ' '.$appParameter->getApplicationTemplateParameter()->getName().'='
                            .$appParameter->getValue();
                    }
                }
                $ansibleResult = AnsibleUtility::executeAnsibleModule(
                    $this->container,
                    $entity->getServer()->getHost(),
                    $entity->getApplication()->getApplicationTemplate()->getAnsibleModule(),
                    $arguments
                );
            }
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
            // Ansible stuff
            if( $pleskResult['succes'] ) {
                $arguments = 'action=redeploy vhost='.$entity->getHostname();
                if( $entity->getPleskCapable() ) {
                    $arguments .= ' admin_user='.$entity->getPleskAdminUserName()
                        .' admin_pass='.$entity->getPleskAdminUserPass()
                        .' db_name='.$entity->getPleskDBName()
                        .' db_username='.$entity->getPleskDBUserName()
                        .' db_password='.$entity->getPleskDBUserPass()
                        .' db_host='.$entity->getPleskDBHost()
                        .' git_branch='.$entity->getDeploymentType()->getGitBranch()
                }
                foreach( $entity->getApplication()->getApplicationParameterValues() as $appParameter ) {
                    if( $appParameter->getValue() != '' ) {
                        $arguments .= ' '.$appParameter->getApplicationTemplateParameter()->getName().'='
                            .$appParameter->getValue();
                    }
                }
                $ansibleResult = AnsibleUtility::executeAnsibleModule(
                    $this->container,
                    $entity->getServer()->getHost(),
                    $entity->getApplication()->getApplicationTemplate()->getAnsibleModule(),
                    $arguments
                );
            }
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
            // Ansible stuff
            if( $pleskResult['succes'] ) {
                $arguments = 'action=undeploy vhost='.$entity->getHostname();
                if( $entity->getPleskCapable() ) {
                    $arguments .= ' admin_user='.$entity->getPleskAdminUserName()
                        .' admin_pass='.$entity->getPleskAdminUserPass()
                        .' db_name='.$entity->getPleskDBName()
                        .' db_username='.$entity->getPleskDBUserName()
                        .' db_password='.$entity->getPleskDBUserPass()
                        .' db_host='.$entity->getPleskDBHost()
                        .' git_branch='.$entity->getDeploymentType()->getGitBranch()
                }
                foreach( $entity->getApplication()->getApplicationParameterValues() as $appParameter ) {
                    if( $appParameter->getValue() != '' ) {
                        $arguments .= ' '.$appParameter->getApplicationTemplateParameter()->getName().'='
                            .$appParameter->getValue();
                    }
                }
                $ansibleResult = AnsibleUtility::executeAnsibleModule(
                    $this->container,
                    $entity->getServer()->getHost(),
                    $entity->getApplication()->getApplicationTemplate()->getAnsibleModule(),
                    $arguments
                );
            }
            if( $entity->getPleskCapable() ) {
                $pleskResult = $this->undeployPleskDeployment($entity);
            }
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
            // Ansible stuff
            if( $pleskResult['succes'] ) {
                $arguments = 'action=delete vhost='.$entity->getHostname();
                if( $entity->getPleskCapable() ) {
                    $arguments .= ' admin_user='.$entity->getPleskAdminUserName()
                        .' admin_pass='.$entity->getPleskAdminUserPass()
                        .' db_name='.$entity->getPleskDBName()
                        .' db_username='.$entity->getPleskDBUserName()
                        .' db_password='.$entity->getPleskDBUserPass()
                        .' db_host='.$entity->getPleskDBHost()
                        .' git_branch='.$entity->getDeploymentType()->getGitBranch()
                }
                foreach( $entity->getApplication()->getApplicationParameterValues() as $appParameter ) {
                    if( $appParameter->getValue() != '' ) {
                        $arguments .= ' '.$appParameter->getApplicationTemplateParameter()->getName().'='
                            .$appParameter->getValue();
                    }
                }
                $ansibleResult = AnsibleUtility::executeAnsibleModule(
                    $this->container,
                    $entity->getServer()->getHost(),
                    $entity->getApplication()->getApplicationTemplate()->getAnsibleModule(),
                    $arguments
                );
            }
            if( $entity->getPleskCapable() && $entity->getDeployed() ) {
                $pleskResult = $this->undeployPleskDeployment($entity);
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
            $entity->getPleskAdminUserPass()
        );
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
                $entity->getPleskAdminUserName()
            );
            $adminResultXML = simplexml_load_string($adminHandle['result']);
            if( !isset($adminResultXML->system->errcode) &&  !isset($adminResultXML->user->add->result->errcode) && $adminResultXML->user->add->result->status == 'ok' ) {
                $entity->setPleskAdminUserId($adminResultXML->user->add->result->guid);

                // Step 3: create database
                $dbHandle = PleskAPIUtility::createDatabase(
                    $entity->getServer()->getHost(),
                    $entity->getServer()->getPleskUser(),
                    $entity->getServer()->getPleskPassword(),
                    $entity->getPleskSubscriptionId(),
                    $entity->getPleskDBName(),
                    $entity->getApplication()->getApplicationTemplate()->getDatabaseType()->getCode(),
                    $entity->getApplication()->getApplicationTemplate()->getDatabaseType()->getPleskDBId()
                );
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
                        $entity->getPleskDBUserPass()
                    );
                    $dbUserResultXML = simplexml_load_string($dbUserHandle['result']);
                    if( !isset($dbUserResultXML->system->errcode) &&  !isset($dbUserResultXML->database->{'add-db-user'}->result->errcode) && $dbUserResultXML->database->{'add-db-user'}->result->status == 'ok' ) {
                        $entity->setPleskDBUserId($dbUserResultXML->database->{'add-db-user'}->result->id);
                        return array('succes'=>true);
                    } elseif( isset($dbUserResultXML->system->errcode) ) {
                        return array('succes'=>false,'error'=>'Unable to create Plesk database user, error code: '.$dbUserResultXML->system->errcode.' ('.$dbUserResultXML->system->errtext.')');
                    } else {
                        return array('succes'=>false,'error'=>'Unable to create Plesk database user, error code: '.$dbUserResultXML->database->{'add-db-user'}->result->errcode.' ('.$dbUserResultXML->database->{'add-db-user'}->result->errtext.')');
                    }                
                } elseif( isset($dbResultXML->system->errcode) ) {
                    return array('succes'=>false,'error'=>'Unable to create Plesk database, error code: '.$dbResultXML->system->errcode.' ('.$dbResultXML->system->errtext.')');
                } else {
                    return array('succes'=>false,'error'=>'Unable to create Plesk database, error code: '.$dbResultXML->database->{'add-db'}->result->errcode.' ('.$dbResultXML->database->{'add-db'}->result->errtext.')');
                }
            } elseif( isset($adminResultXML->system->errcode) ) {
                return array('succes'=>false,'error'=>'Unable to create Plesk admin user, error code: '.$adminResultXML->system->errcode.' ('.$adminResultXML->system->errtext.')');
            } else {
                return array('succes'=>false,'error'=>'Unable to create Plesk admin user, error code: '.$adminResultXML->user->add->result->errcode.' ('.$adminResultXML->user->add->result->errtext.')');
            }
        } elseif( isset($subscriptionResultXML->system->errcode) ) {
            return array('succes'=>false,'error'=>'Unable to create Plesk subscription/webspace, error code: '.$subscriptionResultXML->system->errcode.' ('.$subscriptionResultXML->system->errtext.')');
        } else {
            return array('succes'=>false,'error'=>'Unable to create Plesk subscription/webspace, error code: '.$subscriptionResultXML->webspace->add->result->errcode.' ('.$subscriptionResultXML->webspace->add->result->errtext.')');
        }
    }

    private function redeployPleskDeployment($entity) {
        // Only ansible actions required for now
        return array('succes'=>true);
    }

    private function undeployPleskDeployment($entity) {
        // Step 1: delete database user
        $dbUserHandle = PleskAPIUtility::deleteDatabaseUser(
            $entity->getServer()->getHost(),
            $entity->getServer()->getPleskUser(),
            $entity->getServer()->getPleskPassword(),
            $entity->getPleskDBUserId()
        );
        $dbUserResultXML = simplexml_load_string($dbUserHandle['result']);
        if( !isset($dbUserResultXML->system->errcode) &&  !isset($dbUserResultXML->database->{'del-db-user'}->result->errcode) && $dbUserResultXML->database->{'del-db-user'}->result->status == 'ok' ) {
            $entity->setPleskDBUserId('');

            // Step 2: delete database
            $dbHandle = PleskAPIUtility::deleteDatabase(
                $entity->getServer()->getHost(),
                $entity->getServer()->getPleskUser(),
                $entity->getServer()->getPleskPassword(),
                $entity->getPleskDBId()
            );
            $dbResultXML = simplexml_load_string($dbHandle['result']);
            if( !isset($dbResultXML->system->errcode) &&  !isset($dbResultXML->database->{'del-db'}->result->errcode) && $dbResultXML->database->{'del-db'}->result->status == 'ok' ) {
                $entity->setPleskDBId('');

                // Step 3: delete admin user
                $adminHandle = PleskAPIUtility::deleteUser(
                    $entity->getServer()->getHost(),
                    $entity->getServer()->getPleskUser(),
                    $entity->getServer()->getPleskPassword(),
                    $entity->getPleskAdminUserId()
                );
                $adminResultXML = simplexml_load_string($adminHandle['result']);
                if( !isset($adminResultXML->system->errcode) &&  !isset($adminResultXML->user->del->result->errcode) && $adminResultXML->user->del->result->status == 'ok' ) {
                    $entity->setPleskAdminUserId('');

                    // Step 4: delete subscription
                    $subscriptionHandle = PleskAPIUtility::deleteSubscription(
                        $entity->getServer()->getHost(),
                        $entity->getServer()->getPleskUser(),
                        $entity->getServer()->getPleskPassword(),
                        $entity->getPleskSubscriptionId()
                    );
                    $subscriptionResultXML = simplexml_load_string($subscriptionHandle['result']);
                    if( !isset($subscriptionResultXML->system->errcode) && !isset($subscriptionResultXML->webspace->del->result->errcode) && $subscriptionResultXML->webspace->del->result->status == 'ok' ) {
                        $entity->setPleskSubscriptionId('');
                        return array('succes'=>true);
                    } elseif( isset($subscriptionResultXML->system->errcode) ) {
                        return array('succes'=>false,'error'=>'Unable to delete Plesk subscription/webspace, error code: '.$subscriptionResultXML->system->errcode.' ('.$subscriptionResultXML->system->errtext.')');
                    } else {
                        return array('succes'=>false,'error'=>'Unable to delete Plesk subscription/webspace, error code: '.$subscriptionResultXML->webspace->del->result->errcode.' ('.$subscriptionResultXML->webspace->del->result->errtext.')');
                    }                    
                } elseif( isset($adminResultXML->system->errcode) ) {
                    return array('succes'=>false,'error'=>'Unable to delete Plesk admin user, error code: '.$adminResultXML->system->errcode.' ('.$adminResultXML->system->errtext.')');
                } else {
                    return array('succes'=>false,'error'=>'Unable to delete Plesk admin user, error code: '.$adminResultXML->user->del->result->errcode.' ('.$adminResultXML->user->del->result->errtext.')');
                }
            } elseif( isset($dbResultXML->system->errcode) ) {
                return array('succes'=>false,'error'=>'Unable to delete Plesk database, error code: '.$dbResultXML->system->errcode.' ('.$dbResultXML->system->errtext.')');
            } else {
                return array('succes'=>false,'error'=>'Unable to delete Plesk database, error code: '.$dbResultXML->database->{'del-db'}->result->errcode.' ('.$dbResultXML->database->{'del-db'}->result->errtext.')');
            }
        } elseif( isset($dbUserResultXML->system->errcode) ) {
            return array('succes'=>false,'error'=>'Unable to delete Plesk database user, error code: '.$dbUserResultXML->system->errcode.' ('.$dbUserResultXML->system->errtext.')');
        } else {
            return array('succes'=>false,'error'=>'Unable to delete Plesk database user, error code: '.$dbUserResultXML->database->{'del-db-user'}->result->errcode.' ('.$dbUserResultXML->database->{'del-db-user'}->result->errtext.')');
        }
    }

    private function generatePleskValues($entity)
    {
        $entity->setPleskAdminUserName(substr($entity->getDeploymentType()->getCode().'-'.$entity->getApplication()->getSlug(),0,16));
        $entity->setPleskAdminUserPass(substr(str_replace('=',md5(uniqid(rand(),true)),base64_encode(md5(uniqid(rand(),true)))),0,20));
        $entity->setPleskDBName(substr($entity->getDeploymentType()->getCode().'_'.$entity->getApplication()->getSlug(),0,32));
        $entity->setPleskDBUserName(substr($entity->getDeploymentType()->getCode().'_'.$entity->getApplication()->getSlug(),0,32));
        $entity->setPleskDBUserPass(substr(str_replace('=',md5(uniqid(rand(),true)),base64_encode(md5(uniqid(rand(),true)))),0,20));
        $entity->setPleskDBHost($entity->getApplication()->getApplicationTemplate()->getDatabaseType()->getPleskDBHost());
        return $entity;
    }
    
    private function createAddEditForm($entity)
    {
        $fb = $this->createFormBuilder($entity);
        $fb->add('hostname','text',array('max_length'=>255,'required'=>true,'label'=>'Hostname'));
        $fb->add('pleskCapable','checkbox',array('required'=>false,'label'=>'Plesk enabled?'));
        $fb->add('application','entity',array('class'=>'DellaertWebappDeploymentBundle:Application','property'=>'name','expanded'=>false,'multiple'=>false,'label'=>'Application'));
        $fb->add('server','entity',array('class'=>'DellaertWebappDeploymentBundle:Server','property'=>'host','expanded'=>false,'multiple'=>false,'label'=>'Server'));
        $fb->add('deploymentType','entity',array('class'=>'DellaertWebappDeploymentBundle:DeploymentType','property'=>'name','expanded'=>false,'multiple'=>false,'label'=>'Type'));
        return $fb->getForm();
    }
}
