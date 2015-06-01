<?php 

namespace AppBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;


// Formulario para el registro de nuevos usuarios:

class MaquinaToSubliniaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
        ->add('maquines','entity',
          array(
            'label'=>'Afegir maquina',
            'class'=>'AppBundle:Maquina',
            'property'=>'familia.nom',
            'label_attr' => array('class' => 'intranet-label'),
            'attr' => array('class' => 'intranet-input'),
        ))
        ->add('Afegir', 'submit',
          array(
            'label'=>'Afegir Maquina',
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