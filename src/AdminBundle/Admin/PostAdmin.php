<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PostAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('parent');
        $formMapper->add('categories');
        $formMapper->add('title', 'text');
        $formMapper->add('text', 'textarea', array('attr' => array('class' => 'ckeditor')));
        $formMapper->add('image', 'sonata_type_model_list', array(), array('link_parameters' => array('context' => 'default')));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title');
        $datagridMapper->add('categories');
        $datagridMapper->add('parent');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('title');
        $listMapper->addIdentifier('categories');
        $listMapper->addIdentifier('parent');
    }
}
