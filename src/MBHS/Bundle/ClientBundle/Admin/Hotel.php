<?php

namespace MBHS\Bundle\ClientBundle\Admin;

use Doctrine\ODM\MongoDB\DocumentManager;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;

class Hotel extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt'
    );

    protected function getTypesForFilter()
    {
        /** @var DocumentManager $dm */
        $dm  = $this->getConfigurationPool()->getContainer()->get('doctrine_mongodb')->getManager();
        $types = $dm
            ->getRepository('MBHSClientBundle:Hotel')
            ->createQueryBuilder('q')
            //->distinct('title')
            ->getQuery()
            ->execute()
            ->toArray()
        ;

        return array_combine($types, $types);
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('internalID')
            ->add('title')
            ->add('city')
            ->add('client', 'sonata_type_model_list', ['btn_delete' => false])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('internalID')
            ->add('title')
            ->add('city')
            ->add('client')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('internalID')
            ->add('title')
            ->add('city')
            ->add('unwelcome count', 'field')
            ->add('client')
            ->add('_action', 'actions', ['actions' => ['show' => [], 'edit' => []]]);
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('internalID')
            ->add('title')
            ->add('city')
            ->add('client')
        ;
    }
}