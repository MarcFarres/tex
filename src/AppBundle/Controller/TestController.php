<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// Entities
use AppBundle\Entity\Of;
use AppBundle\Entity\Test;
use AppBundle\Entity\Linia;
use AppBundle\Entity\Resultat;
use AppBundle\Entity\Mesura;
use AppBundle\Entity\Pes;
use AppBundle\Entity\Densitat;

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



class TestController extends Controller
{
  
  protected $repositoris;

  protected $page_vars;

  protected $em;


  public function controllerIni()
  {
    $doctrine = $this->getDoctrine();
    $this->repositoris = array();
    $this->page_vars = array();
    $this->em = $doctrine->getManager();

    // Obtenim els repositoris de les entities que utilitzarem:

    $this->repositoris['OF'] = $doctrine
        ->getRepository('AppBundle:Of');

    $this->repositoris['Test'] = $doctrine
      ->getRepository('AppBundle:Test');

    $this->repositoris['Resultat'] = $doctrine
      ->getRepository('AppBundle:Resultat');

    $this->repositoris['Mesura'] = $doctrine
      ->getRepository('AppBundle:Mesura');

    $this->repositoris['Maquina'] = $doctrine
      ->getRepository('AppBundle:Maquina');
  }

/**

 pàgina inicial del administrador de tests

*/
    public function indexAction()
    {
      $this->controllerIni();
      $OF_repo = $this->repositoris['OF'] ;

      $OF_list = $this->get('of.manager')->getUnDoneOf();

      return $this->render(
        'AppBundle:content:admin_tests.html.twig',array(
        // array con las OF pendientes de finalizar
        "OF_list" => $OF_list,
        ));
    }

/**
 
 primer pas: crear una nova OF

*/
     public function newOfAction(Request $request)
    {

      $OF = new Of();
      $form = $this->createForm(new NewOFType(), $OF);


      // comprovem si el formulari ja ha sigut enviat
      // -----------------------------------------------------------
      $form->handleRequest($request);

        if ($form->isValid()) { 
          // si el formulari es vàlid el guardem a la base de dades
          $this->get('of.manager')->newOf($OF);

          // nos redirigimos a la página donde se inicia el test
          return $this->redirect($this
            ->generateUrl('iniciar_test',array(
              'id' => $OF->getId()
              )), 301
          );

        }
        return $this->render(
        	'AppBundle:content:new_OF.html.twig',
        	array(
            'form' => $form->createView(), 
        ));
    }

/**
 
 segon pas: iniciar test

*/
  public function iniTestAction($id ,$maquina_id ,Request $request)
  {   
    $this->controllerIni();
    // mostrem les OF sense finalitzar
    $OF_list = $this->get('of.manager')->getUnDoneOf();   
    // recuperamos la OF solicitada
    $OF = $this->repositoris['OF']->find($id);   
    // recuperamos la linia testeada
    $linia = $OF->getLinia(); 
    // variables del frontend
    $this->page_vars['OF'] = $OF;
    $this->page_vars['OF_list'] = $OF_list;
    $this->page_vars['linia'] = $linia;

    $resultat = false;
    $test = false;

    return $this->render(
      'AppBundle:content:Test_ini.html.twig',
      $this->page_vars
    );
  }


/**
 
 tercer pas: iniciar test  AJAX

*/
  public function iniTestAjaxAction(Request $request)
  {   
    $this->controllerIni();
    // recuperamos las variables POST
    $id = $request->request->get('id');
    $maquina_id = $request->request->get('maquina_id');
    // recuperamos los objetos
    $maquina = $this->repositoris['Maquina']->findOneById($maquina_id);
    $OF = $this->repositoris['OF']->findOneById($id);
    $linia = $OF->getLinia(); 
    $test = $OF->getTest();
    // la enviamos al frontend
    $this->page_vars['linia'] = $linia;
    $this->page_vars['OF'] = $OF;
    $this->page_vars['maquina'] = $maquina;

    $resultats = $this->get('of.manager')->getAllResultats($test->getId(),$maquina_id);
    // Enviem la llista de resultats al frontend
    $this->page_vars['resultats'] = $resultats;

    return $this->render(
      'AppBundle:include:new_test.html.twig',
      $this->page_vars
    );
  }


  /**
 
 Renderitzem un nou resultat (new test vision)

*/
public function newTestAction($OF_id , $maquina_id){
  $this->controllerIni();
  $OF = $this->repositoris['OF']->findOneById($OF_id);
  $Maquina = $this->repositoris['Maquina']->findOneById($maquina_id);

  $resultat = $this->get('of.manager')->newResultat($OF,$Maquina);
  return $this->redirectToRoute('render_result',array('resultat_id'=>$resultat->getId()));

}


/**
 
 Renderitzem un resultat (test vision)

*
* Enviem un resultat_id i en generem la página des d'on 
* es realitza el test ( test view )
*
*/
  public function testAction($resultat_id ,Request $request){
    $this->controllerIni();
    // el resultat a renderitzar
    $resultat = $this->repositoris['Resultat']->findOneById($resultat_id);
    $Test = $resultat->getTest();
    $OF = $Test->getOf();
    $Maquina = $resultat->getMaquina();
    $linia = $OF->getLinia();
    // obtenim tots els resultats associats a la maquina i al test
    $resultats = $this->get('of.manager')->getAllResultats($Test->getId(),$Maquina->getId());
    // obtenim les OF sense finalitzar
    $OF_list = $this->get('of.manager')->getUnDoneOf();
    // generem les variables del frontend
    $this->page_vars['resultat'] = $resultat;
    $this->page_vars['OF'] = $OF;
    $this->page_vars['maquina'] = $Maquina;
    $this->page_vars['resultats'] = $resultats;
    $this->page_vars['OF_list'] = $OF_list;
    $this->page_vars['linia'] = $linia;

    // la nova mesura de pes
    $Pes = new Pes(); 

    $mesuraForm = $this->createForm(new NewMesuraType(), $Pes); 
    $mesuraForm->handleRequest($request);
    // si hem introduït una nova mesura de pes ...
    if ($mesuraForm->isValid()) { 
      $this->get('of.manager')->newMesura($resultat,$Pes);
    } // ** end of: formulari d'una nova mesura introduïda **
    // enviem el formulari al frontend
    $this->page_vars['mesuraForm'] = $mesuraForm->createView();

    $resultParamsForm = $this->createForm(new ResultParamsType(), $resultat);
    $resultParamsForm->handleRequest($request);

    if ($resultParamsForm->isValid()) {    
/**
 guardem el resultat. Resultat Done
*/  $this->get('of.manager')->saveResultat($resultat);
    }
    // enviem el formulari al frontend
    $this->page_vars['resultParamsForm'] = $resultParamsForm->createView();  

  return $this->render(
    'AppBundle:content:resultat.html.twig',
    $this->page_vars
  ); 
}



/**
 
 Reobrim un resultat 

*/
  public function reopenResultAction(Resultat $resultat){
    $this->controllerIni();
    if (!$resultat) {
      throw $this->createNotFoundException("El resultat no s'ha trobat");
    }
    // reobrim el test
    $resultat->setDone(false);
    $this->em->persist($resultat);
    $this->em->flush();
    // el renderitzem
    return $this->redirectToRoute('render_result',array('resultat_id'=>$resultat->getId()));
  }

/**
 
 Llegir una mesura

*/  
public function llegirMesuraAction(Request $request)    
{
    $isAjax = $request->isXmlHttpRequest();
    if ($isAjax) {   
      $respuesta = $request->request->get('valor2');
      return new Response($respuesta,200);
    }

    return new Response('Acceso incorrecto al controlador');
}

/**
 
 Borrar un resultat

*/
  public function deleteResultAction(Resultat $resultat,$OF,$Maquina){
    if (!$resultat) {
      throw $this->createNotFoundException("El resultat no s'ha trobat");
    }

    $em = $this->getDoctrine()->getEntityManager();

    $mesures = $resultat->getMesures();

    foreach($mesures as $mesura)
    {
      $em->remove($mesura);
    }

    $em->remove($resultat);
    $em->flush();

    return $this->redirectToRoute('iniciar_test',array('id'=>$OF,'maquina_id'=>$Maquina));
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
    public function getTestMaquinaAction($maquina_id,$OfId){
      $this->controllerIni();
      // recuperem la OF
      $OF = $this->repositoris['OF']->findOneById($OfId);
      // recuperem la maquina
      $Maquina = $this->repositoris['Maquina']->findOneById($maquina_id);
      // recuperem el test
      $Test = $OF->getTest();
      // recuperem l'estat de la prova per a la màquina dins el test
      $resultats = $this->get('of.manager')->getAllResultats($Test->getId(),$maquina_id);
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
          'maquina' => $Maquina, 
          'status' => $status,
      ));
    }

/**
 
 Finalitzar una OF

*/
  public function finalizeOfAction(OF $OF){
    $this->controllerIni();
    $page_vars = array();
    // mostrem les OF sense finalitzar
    $OF_list = $this->get('of.manager')->getUnDoneOf();
    // donem per tancada la OF
      if(!$OF->getDone()){
        $OF->setDone(true);
        // guardem la OF
        $this->em->persist($OF);
        $this->em->flush();
      } 
    // generem les variables del frontend
    $page_vars['OF'] = $OF;
    $page_vars['OF_list'] = $OF_list;

    return $this->render(
      'AppBundle:content:OF_overview.html.twig',
        $page_vars
    );
  }

/**
 
 Visualitzar una OF finalitzada

*/
  public function viewOfAction($OF_id){
    $page_vars = array();
    
    

    $page_vars['prueba'] = 'hola' ;

    return $this
        ->render(
          'AppBundle:content:OF_overview.html.twig',
           $page_vars
           );

  }


/**
 
 Visualitzar un report

*/
  public function viewReportAction($OF_id,$maquina_id){
    $page_vars = array();

    return $this
        ->render(
          'AppBundle:content:OF_overview.html.twig',
           $page_vars
           );
  }


  /**
 
 Report d'una maquina

*/  
  public function maquinaReportAction(Maquina $maquina, Request $request)    
  {


  }

}
