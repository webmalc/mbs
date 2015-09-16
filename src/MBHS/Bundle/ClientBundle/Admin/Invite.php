<?php

namespace MBHS\Bundle\ClientBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class Invite extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt'
    );

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('arrival')
            ->add('departure')
            //->add('guests')
            ->add('hotel.title')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('arrival')
            ->add('departure')
            ->add('guests', 'sonata_type_collection', ['associated_property' => 'firstName'])
            ->add('tripRoutes', 'sonata_type_collection', ['associated_property' => 'address'])
            ->add('hotel.title')
            ->add('hotel.city')
            ->add('_action', 'actions', ['actions' => ['edit' => [], 'view' => []]])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('arrival', 'date')
            ->add('departure', 'date')
            //->add('guests', 'sonata_type_collection')
        ;
    }


    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $filter)
    {
        $filter
            ->add('arrival')
            ->add('departure')
            ->add('hotel.title')
            ->add('hotel.city')
            ->add('guests', 'sonata_type_collection', ['associated_property' => 'firstName'])
        ;
    }
}