<?php

namespace MBHS\Bundle\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base Controller
 */
class BaseController extends Controller
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;


    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);

        $this->dm = $this->get('doctrine_mongodb')->getManager();
    }

    /**
     * Add Access-Control-Allow-Origin header to response
     * @param array $sites
     */
    public function addAccessControlAllowOriginHeaders(array $sites)
    {
        $origin = $this->getRequest()->headers->get('origin');
        foreach ($sites as $site) {
            if ($origin == $site) {
                header('Access-Control-Allow-Origin: ' . $site);
            }
        }
    }

    /**
     * Get entity logs
     * @param object $entity
     * @return \Gedmo\Loggable\Entity\LogEntr[]|null
     */
    public function logs($entity)
    {
        if (empty($entity)) {
            return null;
        }

        $dm = $this->get('doctrine_mongodb')->getManager();
        
        $logs = $dm->getRepository('Gedmo\Loggable\Document\LogEntry')->getLogEntries($entity);
        
        if (empty($logs)) {
            return null;
        }
        
        return array_slice($logs, 0, $this->container->getParameter('mbh.logs.max'));
    }
}
