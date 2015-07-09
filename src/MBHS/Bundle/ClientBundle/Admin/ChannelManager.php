<?php
namespace MBHS\Bundle\ClientBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ChannelManager extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt'
    );

    protected function getTypesForFilter()
    {
        $dm  = $this->getConfigurationPool()->getContainer()->get('doctrine_mongodb')->getManager();
        $types = $dm
            ->getRepository('MBHSClientBundle:Channelmanager')
            ->createQueryBuilder('q')
            ->distinct('title')
            ->getQuery()
            ->execute()
            ->toArray()
        ;

        return array_combine($types, $types);
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', 'text', ['label' => 'Title'])
            ->add('client', 'sonata_type_model_list', ['btn_delete' => false])
            ->add('key', 'number', ['label' => 'Key', 'help' => 'Hotel ID from channelmanager configuration'])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', 'doctrine_mongo_string', ['field_type' => 'choice'], null, [
                'choices' => $this->getTypesForFilter(),
            ])
            ->add('client')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('key')
            ->add('client')
            ->add('createdAt')
            ->add('_action', 'actions', ['actions' => ['edit' => [], 'delete' => []]])
        ;
    }

}