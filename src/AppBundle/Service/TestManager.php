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

class TestManager
{

  private $entityManager;

  public function __construct(\Doctrine\ORM\EntityManager $entityManager){
    $this->entityManager = $entityManager;
  }

/**

  Borra els tests sense Of assignada

*/
  public function cleanResultats(){

    $resultats = $this->entityManager->getRepository('AppBundle:Resultat')
      ->findAll();
      foreach($resultats as $resultat){
         if($resultat->getStatus() == 10){
           $this->delete($resultat);
         }
      }
    return $resultats;
  }

/**

  Recuperar todos los 'resultats' de una maquina en una OF

*/
  public function getAllResultats(Of $OF,$maquina_id){
    $test = $OF->getTest();
    $resultats = $this->entityManager->getRepository('AppBundle:Resultat')
      ->findBy(array(
        'maquina' => $maquina_id,
        'test' => $test->getId(),
        array(
        'hora'=>'DESC',
        )
    ));
    return $resultats;
  }


/**

  crear un nuevo test para una màquina sin una OF asignada

*/
  public function newMaquinaResultat(Maquina $Maquina)
  {
    $em = $this->entityManager;
    // recuperem la sublinia de la maquina
    $sublinia = $Maquina->getSublinia();
    // generem un nou resultat
    $resultat = new Resultat(); 

    // li associem la màquina
    $resultat->setMaquina($Maquina);
    // Mitaja Esperada
    $resultat->setMitjanaEsp( $sublinia->getMitjanaEsp() );
    // Descentratge Màxim
    $resultat->setDescMax( $sublinia->getDescentratge() );
    // Desviació Standard Màxima
    $resultat->setDevMax( $sublinia->getDesv() );
    // Longitud de la mostra
    $resultat->setLongitud( $sublinia->getLongitud() );
    // status d'un test sense OF assignada
    $resultat->setStatus(10);
    // guardem la data i hora d'inici del test
    $resultat->setData(new \DateTime('now'));
    // guardem la data per al calendari
    $resultat->setDataInici(date('Y-m-d'));
    // la hora
    $resultat->setHora(date('H:i:s'));
    // guardem el nou "resultat" i "TimeO" a la base de dades
    $em->persist($resultat);
    $em->flush();   

    return $resultat;
  }
/**

  crear un nuevo test para una màquina dentro de una OF

*/
  public function newResultat(Of $OF,Maquina $Maquina)
  {
  	$em = $this->entityManager;

  	$test = $OF->getTest();
  	// recuperem la sublinia de la maquina
    $sublinia = $Maquina->getSublinia();
    // generem un nou resultat
    $resultat = new Resultat(); 

    // li associem el test
    $resultat->setTest($test);
    // li associem la màquina
    $resultat->setMaquina($Maquina);
    // Mitaja Esperada
    $resultat->setMitjanaEsp( $sublinia->getMitjanaEsp() );
    // Descentratge Màxim
    $resultat->setDescMax( $sublinia->getDescentratge() );
    // Desviació Standard Màxima
    $resultat->setDevMax( $sublinia->getDesv() );
    // Longitud de la mostra
    $resultat->setLongitud( $sublinia->getLongitud() );
    $resultat->setStatus(1);
    // guardem la data i hora d'inici del test
    $resultat->setData(new \DateTime('now'));
    // guardem la data per al calendari
    $resultat->setDataInici(date('Y-m-d'));
    // guardem el nou "resultat" i "TimeO" a la base de dades
    $em->persist($resultat);
    $em->flush();   

    return $resultat;
  }

/**
  
  crear una nova mesura en un 'resultat' donada una mesura de 'pes'

* introduïm una nova mesura de pes en el nostre resultat
*/
  public function newMesura($resultat,$valor)
  {
    $pes = new Pes();
    $pes->setValor($valor);
    $pes->setUnitat('gr');
    $densitat = 0;
    $em = $this->entityManager;
  	$Densitat_repo = $em->getRepository('AppBundle:Densitat');
    $Mesures_repo = $em->getRepository('AppBundle:Mesura');
    // les màquines continues es calculen diferent de la resta
    // ----------------------------------------------------------
    $maquina = $resultat->getMaquina();
    $familia = $maquina->getFamilia();
    $tipus = $familia->getTipus();
  	$longitud = $resultat->getLongitud();

    if($tipus == 'Continuas'){
      $pes_temp = $pes->getValor();
      if($pes_temp!=0){
        $res=$longitud/$pes_temp;
        $densitat = round($res,3);
      }
    }
    else{
      $res = $pes->getValor()/$longitud;
      $densitat = round($res,3);
    }
    // =========================================================

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

*/
  public function saveResultat($resultat){
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

     // recordem quina ha sigut la última OF oberta
     $of_id = $resultat->getTest()->getOf()->getId();
     $maquina = $resultat->getMaquina();
     $maquina->setLastOf($of_id);
     $this->entityManager->persist($maquina);

     $this->entityManager->flush();

     return $resultat;
  }
/**

  guardar un resultat a la base de dades un cop finalitzat
  el resultat no te associada una of

*/
  public function endTest($OF,$test){

    $testOf = $OF->getTest();
    $test->setTest($testOf);
    
    return $this->saveResultat($test);
  }
  /**

  borrar un resultat

*/
  public function delete($resultat){
    $mesures = $resultat->getMesures();
    // borrem les mesures del test
    foreach($mesures as $mesura)
    {
      $this->entityManager->remove($mesura);
    }
    $this->entityManager->remove($resultat);
    $this->entityManager->flush();
  }

/**

  Recuperar els tests d'una data i d'un tipus de máquina

*/
  public function getTestsDateType($data,$tipus){
    $tests_list_cache = array();
    $tests_list = array();

    $families = $this->entityManager->getRepository('AppBundle:Familia')->findBy(array(
      "tipus"=>$tipus,
    )); 
    // per a cada familia del tipus seleccionat en busquem totes les máquines
    foreach($families as $familia)
    {
      $maquines_cache = $familia->getMaquines();
      // afegim els tests de cada maquina a la data seleccionada
      foreach ($maquines_cache as $maquina) {
        $tests_list_cache = $this->entityManager->getRepository('AppBundle:Resultat')->findBy(
          array(
          "data_inici"=>$data,
          "maquina"=>$maquina->getId(),
          ),
          array(
          'id'=>'DESC',
        ));
        if(is_array($tests_list_cache)){
          foreach($tests_list_cache as $test_cache)
          {
            $tests_list[] = $test_cache;
          } // foreach tests_list_cache
        }else{return false;}
      } // foreach maquines cache 
    }  // foreach $families

    return $tests_list;
  }

/**

  Recuperar els tests d'una OF, independentment del temps

*/
  public function getTestsTypeOf(Of $OF,$tipus){
    $tests_list_cache = array();
    $tests_list = array();
    // recuperem el conjunt de proves de la OF
    $testOf = $OF->getTest();
    $testOf_id = $testOf->getId();
    // recuperem els tests de la OF
    //$tests_list_cache = $testOf->getResultats();
    $tests_list_cache = $this->entityManager->getRepository('AppBundle:Resultat')->findBy(
      array(
        'test'=>$testOf_id,
        ),
      array(
        'id'=>'DESC',
        ));
    // filtrem els tests segons el tipus rebut
    foreach($tests_list_cache as $test){
      $maquina_tipus = $test->getMaquina()->getFamilia()->getTipus();
      if($maquina_tipus == $tipus){
        $tests_list[] = $test;
      }
    }
    // si no hem trobat cap test ...
    //if(!count($tests_list)){return false;}

    return $tests_list;
  }

/**
  
  ^active Of
  Recuperar els tests d'un tipus de máquina per a OF actives

*/
  public function getTestsTypeActiveOf($data=false,$tipus){
    $tests_list_cache = array();
    $tests_list = array();

    $families = $this->entityManager->getRepository('AppBundle:Familia')->findBy(array(
      "tipus"=>$tipus,
    )); 
    // per a cada familia del tipus seleccionat en busquem totes les máquines
    foreach($families as $familia)
    {
      $maquines_cache = $familia->getMaquines();
      // afegim els tests de cada maquina a la data seleccionada
      foreach ($maquines_cache as $maquina) {
        $tests_list_cache = $this->entityManager->getRepository('AppBundle:Resultat')->findBy(array(
          "data_inici"=>$data,
          "maquina"=>$maquina->getId(),
        ));
        foreach($tests_list_cache as $test_cache)
        {
          $tests_list[] = $test_cache;
        } // foreach tests_list_cache
      } // foreach maquines cache 
    }  // foreach $families

    return $tests_list;
  }

/**

  Recuperar els tests d'una máquina per a una data

*/
  public function getTestsDateMaquina($data,Maquina $maquina){
    
    $tests_list = array();
  
    $tests_list = $this->entityManager->getRepository('AppBundle:Resultat')->findBy(array(
      "data_inici"=>$data,
      "maquina"=>$maquina->getId(),
      ),
      array(
      "hora"=>"DESC",
      )
    );

    return $tests_list;
  }

/**

  Recuperar els tests d'un tipus de máquina

*/
  public function getTestsType($tipus){
    $families = array();
    $tests_list = array();
    $maquina_tests = array();

      $families = $this->entityManager->getRepository('AppBundle:Familia')->findBy(array(
      'tipus'=>$tipus,
      ));
   
      foreach($families as $familia){
        $maquines = $familia->getMaquines();
          foreach($maquines as $maquina){
            $maquina_id = $maquina->getId();
    
            $maquina_tests = $this->entityManager->getRepository('AppBundle:Resultat')->findBy(array(
              'maquina' => $maquina_id,),
            array(
              'id'=>'DESC')
            );
            
              foreach($maquina_tests as $maquina_test){
                $tests_list[] = $maquina_test;
          } // foreach3
        } // foreach 2 
      } // foreach 1
  
    return $tests_list;
  }


  /**

  Recuperar les màquines d'un tipus i les ordena per linies

*/
  public function getMaquinesOfType($tipus){
    $maquines = array();
    // recuperem les families del tipus seleccionat
    $families = $this->entityManager->getRepository('AppBundle:Familia')->findBy(array(
      'tipus'=>$tipus));
    // recuperem les màquines que pertanyen a aquestes families
      foreach($families as $familia){
        $maquines_cache = $familia->getMaquines();
        foreach($maquines_cache as $maquina){
          $maquines[] = $maquina;
        }
      } // foreach
    return $maquines;
  }
}