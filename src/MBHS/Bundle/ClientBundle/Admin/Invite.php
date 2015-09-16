<?php

namespace MBHS\Bundle\ClientBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

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
            ->add('hotel.title')
        ;
    }
}