<?php 

namespace AppBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;


// Formulario para el registro de nuevos usuarios:

class EditSubliniaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
        ->add('numero','number',
          array(
            'label'=>'Sublinia',
            'label_attr' => array('class' => 'intranet-label'),
            'attr' => array(
                'class' => 'intranet-input readonly',
                'step'=>'any',
                //'readonly'=>true,
                ),
        ))
        ->add('linia','entity', 
          array(
            'label'=>'Linia de producció',
            'class'=>'AppBundle:Linia',
            'property'=>'numero',
            'label_attr' => array('class' => 'intranet-label'),
            'attr' => array(
                'class' => 'intranet-input readonly',
                'readonly'=>true,
                ),
        ))
        ->add('familia','entity',
          array(
            'label'=>'Familia de maquines',
            'class'=>'AppBundle:Familia',
            'property'=>'nom',
            'label_attr' => array('class' => 'intranet-label'),
            'attr' => array(
                'class' => 'intranet-input readonly',
                'readonly' => true, 
                ),
        ))
        ->add('mitjana_esp','number',
          array(
            'label'=>'  Mitjana esperada',
            'label_attr' => array('class' => 'intranet-label'),
            'attr' => array(
                'class' => 'intranet-input',
                'step'=>'0.001'
                ),
        ))
        ->add('longitud','number',
          array(
            'label'=>'  Longitud de les mostres',
            'label_attr' => array('class' => 'intranet-label'),
            'attr' => array(
                'class' => 'intranet-input',
                ),
        ))
        ->add('descentratge','number',  
          array(
            'label'=>'  Descentratge (%)',
            'label_attr' => array('class' => 'intranet-label'),
            'attr' => array(
                'class' => 'intranet-input',
                'step'=>'0.001'
                ),
        ))
        ->add('desv','number', 
          array(
            'label'=>'  Desviació std permesa(%)',
            'label_attr' => array('class' => 'intranet-label'),
            'attr' => array(
                'class' => 'intranet-input',
                'step'=>'0.001'
                ),
        ))
        ->add('Actualitzar', 'submit',
          array(
            'label'=>'Crear Sublinia',
            'attr' => array('class' => 'intranet-submit'),
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