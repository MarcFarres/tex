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
use AppBundle\Entity\TimeO;

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
    // recuperem les maquines de la linia
    $linia = $OF->getLinia();
    $sublinies = $linia->getSublinies();

    foreach($sublinies as $sublinia){
      $maquines = $sublinia->getMaquines();
      foreach($maquines as $maquina){
        // generem un nou test per a cada màquina
        $this->newResultat($OF,$maquina);
      }// maquines
    }// sublinies

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


/**

  crear un nuevo test para una màquina dentro de una OF

*
*
*/
  public function newResultat(Of $OF,Maquina $maquina)
  {
  	$em = $this->entityManager;

  	$test = $OF->getTest();
  	// recuperem la sublinia de la maquina
    $sublinia = $maquina->getSublinia();
    // generem un nou resultat
    $resultat = new Resultat(); 
    // generem un nou objecte de temps
    $today = date('d-m-Y');
    // recuperem la TimeO d'avui, si existeix
    $TimeO = $em->getRepository('AppBundle:TimeO')
      ->findOneBy(array(
        'data' => $today,
    ));
      // si no existeix en creem una i la guardem
      if(!$TimeO){
        $TimeO = new TimeO();
        $em->persist($TimeO);
      }
    // li associem el TimeO
    $resultat->setTime($TimeO);
    // li associem el test
    $resultat->setTest($test);
    // li associem la màquina
    $resultat->setMaquina($maquina);
    // guardem el resultat a la base de dades
    // introduim els parametres amb que treballarà el resultat
    // Mitaja Esperada
    $resultat->setMitjanaEsp( $sublinia->getMitjanaEsp() );
    // Descentratge Màxim
    $resultat->setDescMax( $sublinia->getDescentratge() );
    // Desviació Standard Màxima
    $resultat->setDevMax( $sublinia->getDesv() );
    // Longitud de la mostra
    $resultat->setLongitud( $sublinia->getLongitud() );
    $resultat->setStatus(1);
    // guardem la data d'inici del test
    $resultat->setData(new \DateTime('now'));
    // guardem el nou "resultat" i "TimeO" a la base de dades
    
    $em->persist($resultat);
    $em->flush();   

    return $resultat;
  }

}