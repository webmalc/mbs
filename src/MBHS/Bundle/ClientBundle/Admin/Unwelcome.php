<?php

namespace MBHS\Bundle\ClientBundle\Admin;

use Doctrine\ODM\MongoDB\DocumentManager;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class Unwelcome
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class Unwelcome extends Admin
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
            ->getRepository('MBHSClientBundle:Unwelcome')
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
            //->add('tourist', 'sonata_type_model_list', ['label' => 'Tourist'])
            //->add('tourist.firstName')
            ->add('client', 'sonata_type_model_list', ['btn_delete' => false])
            ->add('isAggressor')
            ->add('comment', 'textarea')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('client')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('tourist.firstName')
            ->addIdentifier('tourist.lastName')
            ->addIdentifier('tourist.birthday')
            //->add('client', 'sonata_type_model_list')
            ->add('comment')
            ->add('isAggressor')
            ->add('createdAt')
            ->add('_action', 'actions', ['actions' => ['edit' => [], 'delete' => []]])
        ;
    }
}