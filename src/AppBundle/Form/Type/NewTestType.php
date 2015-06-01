<?php 

namespace AppBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;


// Formulario para el registro de nuevos usuarios:

class NewOFType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
            ->add('numero','text',
                array('label'=>'  Identificador',
                    'label_attr' => array('class' => 'intranet-label icon-mail'),
                    'attr' => array('class' => 'intranet-input'),
            ))
            ->add('descripcio','textarea', 
                array(
                  'label'=>'  Descripció',
                  'label_attr' => array('class' => 'intranet-label icon-user'),
                  'attr' => array('class' => 'intranet-input'),
            ))
            ->add('color','text',
                array('label'=>'  Color',
                    'label_attr' => array('class' => 'intranet-label icon-lock'),
                    'attr' => array('class' => 'intranet-input'),
            ))
            ->add('num_partida','text',
                array(
                  'label'=>'Partida', 
                  'label_attr' => array('class' => 'intranet-label icon-text'),
                  'attr' => array('class' => 'intranet-input'),
            ))
            ->add('linia','entity',
                array(
                  'label'=>'Linia de produccio', 
                  'label_attr' => array('class' => 'intranet-label icon-text'),
                  'class'=>'AppBundle:Linia',
                  'property'=>'numero',
                  'attr' => array('class' => 'intranet-input'),
            ))
            ->add('Crear', 'submit', 
                array(
                    'attr' => array('class' => 'intranet-submit'),
            ));
            

            //->add('save','submit')
        
        
        // Algunos elementos de utilidad, tercer parámetro:
        /*
            .- array('mapped' => false)
                ** se usa para campos que no están mapeados en el objeto que sostiene
                 los datos del formulario, en este caso "Entity/User"
        */
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