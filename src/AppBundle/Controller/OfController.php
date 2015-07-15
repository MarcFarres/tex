<?php

namespace AppBundle\Controller;

use AppBundle\Controller\BaseController;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Process\Process;

use AppBundle\Constant\Days;

// Entities
use AppBundle\Entity\Of;
use AppBundle\Entity\Test;
use AppBundle\Entity\Linia;
use AppBundle\Entity\Resultat;
use AppBundle\Entity\Mesura;
use AppBundle\Entity\Maquina;
use AppBundle\Entity\Pes;
use AppBundle\Entity\Densitat;

// conexió al port serial
use AppBundle\Utils\PhpSerial ;

// Model
//use AppBundle\Model\ResultatTest as Resultat;

// formularis
use AppBundle\Form\Type\NewOFType ;
use AppBundle\Form\Type\NewTestType ;
use AppBundle\Form\Type\ResultParamsType ;
use AppBundle\Form\Type\ResultType ;
use AppBundle\Form\Type\NewMesuraType ;

// utils
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// constants
use AppBundle\Constant\Status ;



class OfController extends BaseController
{ 


/**

 ^index
 pàgina inicial del administrador de tests
 > active_OF (/active_of)
 
*/
  public function indexAction(Request $request)
  {
    $OF = new Of();
    $form = $this->createForm(new NewOFType(), $OF);
    $form->handleRequest($request);

    if ($form->isValid()) { 
    // si el formulari es vàlid guardem la OF a la base de dades
      $this->get('of.manager')->newOf($OF);
    }
    return $this->render('AppBundle:content:active_of.html.twig',array(
      'form'=>$form->createView(), 
    ));
  }

/**
 ^edit_of
 Editar una OF
 >editar_OF (/OF/editar_OF/{OF})
*/
     public function editOfAction(OF $OF, Request $request)
    {
      
      $this->controllerIni();
      $form = $this->createForm(new NewOFType(), $OF);
      // comprovem si el formulari ja ha sigut enviat
      // -----------------------------------------------------------
      $form->handleRequest($request);

        if ($form->isValid()) { 
          // si el formulari es vàlid el guardem a la base de dades
          //$this->get('of.manager')->newOf($OF);
          $this->em->persist($OF);
          $this->em->flush();
          // nos redirigimos a la página donde se inicia el test
          return $this->redirect($this->generateUrl('active_OF'), 301);
        }

        return $this->render(
          'AppBundle:content:edit_OF.html.twig',array(
          'form' => $form->createView() ));
    }

/**
 
 ^activate of
 Activar una OF
 -> OF_activate (/OF/editar_OF/{OF})
 <-

*/
     public function activateOfAction(OF $OF)
    {
      $this->controllerIni();
      $OF->setDone(0);
      $this->em->persist($OF);
      $this->em->flush();

      $newOF = new Of();
      $form = $this->createForm(new NewOFType(), $newOF);
     
      return $this->render('AppBundle:content:active_of.html.twig',array(
      'form'=>$form->createView(), 
      ));
    }
/**
 
 ^finalize
 Finalitzar una OF
 >finalitzar_OF (/OF/finalize_OF/{OF})

*/
  public function finalizeOfAction(OF $OF,Request $request){
    $this->controllerIni();
    
    // donem per tancada la OF
      if(!$OF->getDone()){
        $OF->setDone(true);
        // guardem la OF
        $this->em->persist($OF);
        $this->em->flush();
      } 
    $OF = new Of();
    $form = $this->createForm(new NewOFType(), $OF);
    $form->handleRequest($request);

    if ($form->isValid()) { 
    // si el formulari es vàlid guardem la OF a la base de dades
      $this->get('of.manager')->newOf($OF);
    }
    return $this->render('AppBundle:layout:admin_tests.html.twig',array(
      'form'=>$form->createView(), 
    ));
  }
/**
 
 ^view_of
 Visualitzar una OF en detall
 >OF_view (/OF/OF_overview/{OF_id})

*/
  public function viewOfAction(Of $OF ,Request $request){   
    $this->controllerIni();
    // recuperamos la linia testeada
    $linia = $OF->getLinia(); 

    return $this->render(
      'AppBundle:content-layout:OF_overview.html.twig',
    array(
      // variables del frontend
      'OF' => $OF,
      'linia' => $linia,
    ));
  }
/**
 
 ^delete_of
 Borra una OF no finalitzada
 > delete_of  (/OF/borrar/{OF})

*/
public function deleteOFAction($OF) 
  {
    // borrem la OF oberta
    $this->get('of.manager')->removeOf($OF);
    return $this->redirect($this->generateUrl('active_OF',array('OF' => $OF)), 301);
}

/**
 
 Mostrar els tests realitzats (Historial)

*/
    public function showHistorialAction(){
      // mostrem les OF finalitzades
      $OF_list = $this->get('of.manager')->getDoneOf();
      
      return $this->render(
          'AppBundle:content:tests_historial.html.twig',
          array(
            "OF_list" => $OF_list,
        ));
    }

/**

 Mostrar una màquina en funció de l'estat dels seus tests en una OF

*/
    public function getTestMaquinaAction(Maquina $maquina,Of $OF){
      // recuperem l'estat de la prova per a la màquina dins el test
      $resultats = $this->get('test.manager')->getAllResultats($OF,$maquina->getId());
      $status = false;

      if(!$resultats || !count($resultats)){
        // No se ha iniciado ningún test
        $status = 'no_test';
      }
      else{
        $resultat_done = true;
        $resultat_ok = true;

        foreach($resultats as $resultat){
           $finalized = $resultat->getDone();
           $approved = $resultat->getTestOk();
           if(!$finalized){$resultat_done = false;} 
           if(!$approved){$resultat_ok = false;} 
        }
        if(!$resultat_done){
          // se ha iniciado algún test que no se ha concluido
          $status = 'not_finished';
        }
        elseif($resultat_done && !$resultat_ok){
          // se ha finalizado un test pero con algún error
          $status = 'errors';
        }
        elseif($resultat_done && $resultat_ok){
          // se han finalizado los tests sin ningún error
          $status = 'test_ok';
        }
      }
      
      return $this->render(
        'AppBundle:include:maquina.html.twig',
        array(
          'Of' => $OF,
          'maquina' => $maquina, 
          'status' => $status,
      ));
  }

/**

 Carrega la llista de OF's sense finalitzar
 > No-route

*/
  public function listOfPendentsAction(){
    // recuperem les OF sense finalitzar
    $OF_list = $this->get('of.manager')->getUnDoneOf();
    return $this->render(
      'AppBundle:ajax:list_OF_pendents.html.twig',array(
      "OF_list" => $OF_list ));
  }
}