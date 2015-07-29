<?php
namespace MBHS\Bundle\BaseBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;

class Version extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt'
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', 'text', ['label' => 'Number', 'help' => 'version number'])
            ->add(
                'description',
                'textarea',
                [
                    'label' => 'Description',
                    'required' => false,
                    'help' => 'Description of the the version. HTML tags are allowed',
                    'attr' => ['class' => 'tinymce']
                ]
            );
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('description', null, ['safe' => true]);

    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title', null, ['route' => ['name' => 'show']])
            ->add('createdAt', 'datetime')
            ->add('_action', 'actions', ['actions' => ['edit' => [], 'delete' => []]]);
    }
}