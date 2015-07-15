<?php 

namespace AppBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;



class assignarOfType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
        ->add('test','entity',
          array(
            'label'=>'Lista de OF',
            'class'=>'AppBundle:Test',
            'property'=>'of.formName',
            'label_attr' => array('class' => 'intranet-label'),
            'attr' => array('class' => 'intranet-input'),
        ))
        ->add('Asignar', 'submit',
          array(
            'attr' => array(
              'class' => 'intranet-repeat',
              'value' => 'Asignar OF'),
        ));
    }

    public function getName()
    {
     
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults([
        //'data_class' => 'Acme\TaskBundle\Entity\Task',
        'attr' => [
          'class' => 'intranet-form',
          'id' => 'endResult',
          ]
      ]);
    }
}