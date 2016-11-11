<?php

// src/AdminBundle/Admin/DoctorAdmin.php
namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class DoctorAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Informations', array('class' => 'col-md-6'))
                    ->add('firstname', 'text')
                    ->add('lastname')
                    ->add('email')
                    ->add('speciality')
                    ->add('description')
                    ->add('formation')
                    ->add('insurance')
            ->end()
            ->with('Meta data', array('class' => 'col-md-6'))
                ->add('adress')
                ->add('zipcode')
                ->add('city')
                ->add('phoneNumber')
                ->add('officePhoneNumber')
            ->end()
        ;

    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('firstname');
        $datagridMapper->add('speciality');
        $datagridMapper->add('insurance');
        $datagridMapper->add('city');
        $datagridMapper->add('zipcode');

    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('firstname');
        $listMapper->addIdentifier('lastname');
        $listMapper->addIdentifier('email');
        $listMapper->addIdentifier('speciality');
        $listMapper->addIdentifier('city');
        $listMapper->addIdentifier('zipCode');
        $listMapper->add('insurance');
    }
}