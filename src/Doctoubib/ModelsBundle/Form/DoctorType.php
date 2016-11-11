<?php

namespace Doctoubib\ModelsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DoctorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add('formation')
            ->add('description')
            ->add('adress')
            ->add('zipcode')
            ->add('city')
            ->add('phoneNumber')
            ->add('officePhoneNumber')
            ->add('speciality')
            ->add('insurance')        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Doctoubib\ModelsBundle\Entity\Doctor'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'doctoubib_modelsbundle_doctor';
    }


}
