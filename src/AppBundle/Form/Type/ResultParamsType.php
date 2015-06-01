<?php 

namespace AppBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;



class ResultParamsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
        ->add('mitjana_esp','number',
        array(
          'label'=>' Mitjana esperada',
          'label_attr' => array('class' => 'intranet-label'),
          'attr' => array(
            'class' => 'intranet-input',
            'step'=>'0.001',
            ),
        ))
        ->add('desc_max','number',
        array(
          'label'=>' Descentratge Màxim',
          'label_attr' => array('class' => 'intranet-label'),
          'attr' => array(
            'class' => 'intranet-input',
            'step'=>'0.001',
            ),
        ))
        ->add('dev_max','number',
        array(
          'label'=>' Desv. Màxima',
          'label_attr' => array('class' => 'intranet-label'),
          'attr' => array(
            'class' => 'intranet-input',
            'step'=>'0.001',
            ),
        ))
        ->add('longitud','number',
        array(
          'label'=>' Longitud de la mostra',
          'label_attr' => array('class' => 'intranet-label'),
          'attr' => array(
            'class' => 'intranet-input',
            ),
        ))
        ->add('Finalitzar_test', 'submit',
          array(
            'attr' => array(
              'class' => 'intranet-accept',
              'value' => 'Finalitzar test'),
        ));
    }

    public function getName()
    {
     
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults([
        //'data_class' => 'Acme\TaskBundle\Entity\Task',
        'attr' => ['class' => 'intranet-form']
      ]);
    }
}