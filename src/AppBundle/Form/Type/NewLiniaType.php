<?php 

namespace AppBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;


// Formulario para el registro de nuevas linias de producción:

class NewLiniaType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('numero','text',
        array(
          'label'=>' Identificador de la linia',
          'label_attr' => array('class' => 'intranet-label'),
          'attr' => array('class' => 'intranet-input'),
      ))
      ->add('Crear', 'submit',
        array(
          'label'=>' Crear nova linia',
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