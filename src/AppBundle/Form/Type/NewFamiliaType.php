<?php 

namespace AppBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;


// Formulario para el registro de nuevos usuarios:

class NewFamiliaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
        ->add('nom','text',
          array(
            'label'=>'  Nom complert de la familia',
            'label_attr' => array('class' => 'intranet-label'),
            'attr' => array('class' => 'intranet-input'),
        ))
        ->add('tipus','text',
          array(
            'label'=>'  Tipus de màquina',
            'label_attr' => array('class' => 'intranet-label'),
            'attr' => array('class' => 'intranet-input'),
        ))
        ->add('model','text',
          array(
            'label'=>'  Model de la màquina',
            'label_attr' => array('class' => 'intranet-label'),
            'attr' => array('class' => 'intranet-input'),
        ))
        ->add('Crear', 'submit',
          array(
            'label'=>'Crear Familia',
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