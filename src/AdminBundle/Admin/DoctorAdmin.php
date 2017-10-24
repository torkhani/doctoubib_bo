<?php

// src/AdminBundle/Admin/DoctorAdmin.php
namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\Type\CollectionType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class DoctorAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Informations personnelles', array('class' => 'col-md-4'))
                    ->add('civility', 'choice', array(
                        'choices' => array(
                            'Mr.' => 'mr',
                            'Mme.' => 'mme'
                        ),
                        'expanded' => true,
                        'data' => 'mr'
                    ))
                    ->add('firstname')
                    ->add('lastname')
                    ->add('email')
                    ->add('description')
            ->end()

            ->with('Informations professionelle', array('class' => 'col-md-4'))
            ->add('specialities', 'sonata_type_model', array(
            'multiple' => true,
            ))
            ->add('formations')
            ->add('experiences')
            ->add('publications')
            ->end()

            ->with('Informations d\'accès', array('class' => 'col-md-4'))
                ->add('phoneNumber')
            ->end()

            ->with('Autres informations', array('class' => 'col-md-4'))
            ->add('insurance')
            ->end()

            ->with('Consutation', array('class' => 'col-md-4'))
            ->add('consultations')
            ->add('offices', 'sonata_type_collection', array(
                'by_reference' => false
            ))
            ->end()
        ;

    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('firstname');
        $datagridMapper->add('specialities');
        $datagridMapper->add('insurance');

    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('firstname');
        $listMapper->addIdentifier('lastname');
        $listMapper->addIdentifier('email');
        $listMapper->addIdentifier('specialities');
        $listMapper->add('insurance');
    }
}
