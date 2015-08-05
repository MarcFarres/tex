<?php 

namespace AppBundle\Service;

use AppBundle\Entity\Of;
use AppBundle\Entity\Maquina as Maquina;

// entidades utilizadas
use AppBundle\Entity\Resultat;
use AppBundle\Entity\Pes;
use AppBundle\Entity\Densitat;
use AppBundle\Entity\Mesura;
use AppBundle\Entity\Test;

class OfManager
{

  private $entityManager;

  public function __construct(\Doctrine\ORM\EntityManager $entityManager){
    $this->entityManager = $entityManager;
  }


/**

  crear y guardar una nueva OF

*
*
*/
  public function newOf(Of $OF){

    // la data de creació de la OF
    $OF->setData(new \DateTime('today') );   
    // creem un nou test
    $test = new Test();
    // l'assignem a la OF
    $OF->setTest($test);
    // guardem el test
    $this->entityManager->persist($test);
    
    $color = $OF->getColor();
      $num = $OF->getNumero();
      $formName = $num." / ".$color;
      $OF->setFormName($formName); 

  $OF->setDone(false);
  // guardem la nova OF
  
  $this->entityManager->persist($OF);
  $this->entityManager->flush();

  }
/**

  recuperar las OF aún sin finalizadar

*
*
*/
  public function getUnDoneOf()
  {
    $OF_list = $this->entityManager->getRepository('AppBundle:Of')
      ->findBy(array(
        'done' => false,
      ),array(
        'data' => 'DESC',
    ));

      return $OF_list;
  }

/**

  borrar una OF

*
*
*/
  public function removeOf($OF)
  {
    $OF_to_remove = $this->entityManager->getRepository('AppBundle:Of')
      ->findOneById($OF);
    
    // borrem la OF
    $this->entityManager->remove($OF_to_remove);
    $this->entityManager->flush();
  }

/**

 recupera les maquines d'un tipus i les ordena per linies

*/
  public function get_maquines($tipus){
    // la llista de màquines ordenades per linies
    $list_of_maquines = array();
    //recuperem les families del tipus demandat
    $families = $this->entityManager->getRepository('Familia')->findBy(array(
      'tipus' => $tipus ));
    foreach($families as $familia){
      // les maquines de la familia
      $maquines = $familia->getMaquines();
      foreach($maquines as $maquina){
        // la linia
        $linia = $maquina->getLinia();
        // el númer de la linia
        $numero = $linia->getNumero();

        $list_of_maquines[$numero] = array(
          'id'=>$maquina->getId(),
          'numero'=>$maquina->getNumero(),
          );
      }
    }
    return $list_of_maquines;
  }
/**

 recuperar las OF ya finalizadas

*/
  public function getDoneOf(){
    $OF_list = $this->entityManager->getRepository('AppBundle:Of')
      ->findBy(array(
        'done' => true,
      ),array(
        'data' => 'DESC',
    ));

      return $OF_list;
  }

}