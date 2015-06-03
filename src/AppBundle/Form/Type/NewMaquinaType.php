<?php 

namespace AppBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;


// Formulario para el registro de nuevos usuarios:

class NewMaquinaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
        ->add('numero','text',
          array(
            'label'=>'  Numero de màquina',
            'label_attr' => array('class' => 'intranet-label'),
            'attr' => array('class' => 'intranet-input'),
        ))
        ->add('familia','entity',
          array(
            'label'=>'Familia de maquines',
            'class'=>'AppBundle:Familia',
            'property'=>'nom',
            'label_attr' => array('class' => 'intranet-label'),
            'attr' => array('class' => 'intranet-input'),
        ))
        ->add('linia','entity',
          array(
            'label'=>'Linia de produccio',
            'class'=>'AppBundle:Linia',
            'property'=>'numero',
            'label_attr' => array('class' => 'intranet-label'),
            'attr' => array('class' => 'intranet-input'),
        ))
        ->add('sublinia','entity',
          array(
            'label'=>'Sublinia de produccio',
            'class'=>'AppBundle:Sublinia',
            'property'=>'numero',
            'label_attr' => array('class' => 'intranet-label'),
            'attr' => array('class' => 'intranet-input'),
        ))
        ->add('Crear', 'submit',
          array(
            'label'=>'Crear Maquina',
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