<?php
namespace MBHS\Bundle\ClientBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;

class Client extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'title'
    );

    public function getNewInstance()
    {
        $instance = parent::getNewInstance();
        $instance->setKey(
            $this->getConfigurationPool()->getContainer()->get('mbhs.helper')->getRandomString(40)
        );

        return $instance;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', 'text', ['label' => 'Title', 'help' => 'unique client title'])
            ->add('email', 'email', ['label' => 'E-mail', 'help' => 'unique client e-mail address'])
            ->add('phone', 'text')
            ->add('person', 'text', ['required' => false])
            ->add('url', 'url', ['label' => 'Url', 'help' => 'unique client url address'])
            ->add('ip', 'text')
            ->add('key', 'text', ['label' => 'Key', 'help' => 'client 40-character secret key'])
            ->add('note', 'textarea', ['required' => false])
            ->add('version', 'sonata_type_model_list', ['btn_delete' => false, 'btn_add' => false])
            ->add('isEnabled', 'checkbox', ['label' => 'Enabled?', 'required' => false])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('email')
            ->add('phone')
            ->add('url')
            ->add('ip')
            ->add('version')
            ->add('isEnabled')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('email')
            ->add('phone')
            ->add('person')
            ->add('url', 'url')
            ->add('ip')
            ->add('key')
            ->add('note')
            ->add('version')
            ->add('isEnabled', 'boolean')
            ->add('lastLogin', 'datetime')
            ->add('createdAt', 'datetime')
            ->add('updatedAt', 'datetime')
            ->add('createdBy')
            ->add('updatedBy')
            ->add('channelManagers')
        ;

    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title', null, ['route' => ['name' => 'show']])
            ->add('email')
            ->add('phone')
            ->add('url', 'url')
            ->add('isEnabled', 'boolean', ['editable' => true])
            ->add('version')
            ->add('_action', 'actions', ['actions' => ['edit' => [], 'delete' => []]])
        ;
    }

}