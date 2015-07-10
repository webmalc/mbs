<?php
namespace MBHS\Bundle\ClientBundle\Admin;

use MBHS\Bundle\ClientBundle\Document\Package as PackageDoc;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class Package extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt'
    );

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('number')
            ->add('type', 'doctrine_mongo_string', ['field_type' => 'choice'], null, [
                'choices' => array_combine(PackageDoc::TYPES, PackageDoc::TYPES),
            ])
            ->add('client')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('key')
            ->add('number')
            ->add('type')
            ->add('begin', 'date')
            ->add('end', 'date')
            ->add('hotel')
            ->add('roomType')
            ->add('payer')
            ->add('price')
            ->add('client')
            ->add('createdAt', 'datetime')
            ->add('_action', 'actions', ['actions' => ['delete' => []]])
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'show', 'batch', 'delete', 'export']);
    }
}