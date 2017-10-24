<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class OfficeAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Informations professionelle', array('class' => 'col-md-4'))
            ->add('name')
            ->add('phone')
            ->add('phoneSecond')
            ->add('fax')
            ->end()

            ->with('Informations d\'accès', array('class' => 'col-md-4'))
            ->add('address')
            ->add('floor')
            ->add('intercom')
            ->add('zipcode')
            ->add('region')
            ->add('city')
            ->add('longitude')
            ->add('latitude')
            ->add('digicode')
            ->add('elevator')
            ->add('handicapAccess')
            ->end()
            ->with('Doctors', array('class' => 'col-md-4'))
            ->add('doctor')
            ->end()
        ;

    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('region');
        $datagridMapper->add('city');
        $datagridMapper->add('zipcode');
        $datagridMapper->add('doctor');

    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
        $listMapper->addIdentifier('region');
        $listMapper->addIdentifier('city');
        $listMapper->addIdentifier('zipcode');
        $listMapper->addIdentifier('doctor');
    }
}
