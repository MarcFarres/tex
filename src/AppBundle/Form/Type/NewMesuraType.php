<?php 

namespace AppBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;


// Formulario para el registro de nuevos usuarios:

class NewMesuraType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
            ->add('valor','number',
              array(
              'label'=>'  Valor de la mesura',
              'label_attr' => array('class' => 'intranet-label'),
              'attr' => array(
                'class' => 'intranet-input',
                'step'=>'0.0001'
                ),
            ))
            ->add('Acceptar', 'submit', 
                array(
                    'attr' => array('class' => 'intranet-accept'),
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