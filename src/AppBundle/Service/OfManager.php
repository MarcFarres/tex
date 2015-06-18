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
  public function newOf(OF $OF){
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

 resuperar las OF ya finalizadas

*
*
*/
  public function getDoneOf()
  {
    $OF_list = $this->entityManager->getRepository('AppBundle:Of')
      ->findBy(array(
        'done' => true,
      ),array(
        'data' => 'DESC',
    ));

      return $OF_list;
  }


/**

  Recuperar todos los 'resultats' de una maquina en una OF

*
*
*/
  public function getAllResultats($test_id,$maquina_id)
  {

    $resultats = $this->entityManager->getRepository('AppBundle:Resultat')
      ->findBy(array(
        'maquina' => $maquina_id,
        'test' => $test_id,
    ));

    return $resultats;

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

    $resultat = new Resultat(); 
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
    // guardem el nou "resultat" a la base de dades
    $em->persist($resultat);
    $em->flush();   

    return $resultat;
  }

/**
  
  crear una nova mesura en un 'resultat' donada una mesura de 'pes'

*
* introduïm una nova mesura de pes en el nostre resultat
*
*/
  public function newMesura($resultat,$pes)
  {
    $em = $this->entityManager;
  	$Densitat_repo = $em->getRepository('AppBundle:Densitat');
    $Mesures_repo = $em->getRepository('AppBundle:Mesura');
    

  	$longitud = $resultat->getLongitud();
    $densitat = $pes->getValor()/$longitud;

    // creem la nova mesura a guardar
    $Mesures_list = $Mesures_repo->findBy(array(
      'resultat' => $resultat->getId(),
    ));
          
    $num = count($Mesures_list) + 1;
    // creem una nova mesura
    $Mesura = new Mesura($num);
    // li associem el resultat amb el que estem treballant
    $Mesura->setResultat($resultat);
    // calculem la mesura de densitat

    $Densitat = new Densitat();
    // generem la mesura de densitat
    $Densitat->setValor($densitat);
    // associem les dues mesures (pes i densitat) al objecte 'Mesura'
    $Mesura->setDensitat($Densitat);
    $Mesura->setPes($pes);

    // ho guardem tot a la base de dades
    $em->persist($Mesura);
    $em->persist($Densitat);
    $em->persist($pes);

    $em->flush();

    return $Mesura;
  }


/**

  guardar un resultat a la base de dades un cop finalitzat

*
*
*/
  public function saveResultat($resultat)
  {
  	// càlcul dels resultats finals del test
     $resultat
       ->setMitjana()
       ->setDescentratge()
       ->setDev();

     // comprovem l'èxit del test
     $resultat->evaluate();
     // generem el report
     // ( ... )

     $this->entityManager->persist($resultat);
     $this->entityManager->flush();
  }
}