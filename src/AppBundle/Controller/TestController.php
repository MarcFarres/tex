<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Process\Process;

use AppBundle\Constant\Days;

// Entities
use AppBundle\Entity\Of;
use AppBundle\Entity\Test;
use AppBundle\Entity\Linia;
use AppBundle\Entity\Resultat;
use AppBundle\Entity\Mesura;
use AppBundle\Entity\Pes;
use AppBundle\Entity\Densitat;
use AppBundle\Entity\TimeO;

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



class TestController extends Controller
{
  
  protected $repositoris;

  protected $page_vars;

  protected $em;

  protected $process;


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

    $this->repositoris['Pes'] = $doctrine
      ->getRepository('AppBundle:Pes');

    $this->repositoris['Familia'] = $doctrine
      ->getRepository('AppBundle:Familia');

    $this->repositoris['TimeO'] = $doctrine
      ->getRepository('AppBundle:TimeO');

  }

/**

 pàgina inicial del administrador de tests

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
    return $this->render('AppBundle:content:admin_tests.html.twig',array(
      'form'=>$form->createView(), 
    ));
  }

/**

 visió dels tests oberts ( per a entrar mesures )

*/
  public function actualTestsAction()
  {
    $this->controllerIni();
    // recuperem les families de maquines
    $families = $this->repositoris['Familia']->findAll();

    return $this->render('AppBundle:content:actual-tests-overview.html.twig',array(
      'families'=>$families,
    ));

  }

  /**

 (Ajax) finalitzar test y veure la llista dels que queden

*/
  public function saveAndViewAction(Request $request)
  {
    $this->controllerIni();
    $resultat_id = $request->request->get('resultat_id');
    $resultat = $this->repositoris['Resultat']->findOneById($resultat_id);
    $this->get('of.manager')->saveResultat($resultat);
    // la maquina
    $maquina = $resultat->getMaquina();
    // la familia
    $familia = $maquina->getFamilia();
    // el tipus
    $tipus = $familia->getTipus();
    // modifiques el request per a poder renderitzar la llista
    $request->request->set('tipus',$tipus);
    return $this->actualTestsListAction($request);
  }

/**

 (Ajax) La llista dels tests oberts

*/
  public function actualTestsListAction(Request $request)
  {
    $this->controllerIni();
    $tipus = $request->request->get('tipus');
    //recuperem els tipus de maquines
    $families = $this->repositoris['Familia']->findBy(array(
        'tipus' => $tipus,
      ));
    //resuperem els tests a mostrar
    $tests = array();
    foreach($families as $familia){
      // les maquines de la familia
      $maquines = $familia->getMaquines();
      foreach($maquines as $maquina){
        // els tests de cada maquina
        $maquina_id = $maquina->getId();
        $maquina_tests = $this->repositoris['Resultat']->findBy(array(
          'maquina' => $maquina_id,
          // només recuperem els que estan oberts
          'done'=>false),
          array(
          'data'=>'DESC'
        ));

        foreach($maquina_tests as $maquina_test){
          //cadascun dels tests
          $tests[] = $maquina_test;
        }
      }
      
    }

    return $this->render('AppBundle:ajax:actual_tests_list.html.twig',array(
      'resultats'=>$tests
    ));

  }

/**
 
 (AJAX) formulari de nova mesura. Guardem la nova mesura en un resultat

*/
  public function novaMesuraAction(Request $request)
  {
    $this->controllerIni();

    $resultat_id = $request->request->get('resultat_id');
    $valor = $request->request->get('valor');
    // el resultat a renderitzar
    $resultat = $this->repositoris['Resultat']->findOneById($resultat_id);
    
    $Pes = new Pes();
    $Pes->setValor($valor);
    $Pes->setUnitat('gr');
    
    $Mesura = $this->get('of.manager')->newMesura($resultat,$Pes);

    return $this->render('AppBundle:include:mesura_row.html.twig',array(
        'mesura' => $Mesura, 
        'resultat' => $resultat,));
  }


/**
 
 (AJAX) Finalitzar un resultat

*/
  public function endResultatAction(Request $request)
  {
    $this->controllerIni();

    $resultat_id = $request->request->get('resultat_id');
    $resultat = $this->repositoris['Resultat']->findOneById($resultat_id);
    $Test = $resultat->getTest();
    $OF = $Test->getOf();
    $today = date('d-m-Y');

    // recuperem la TimeO d'avui, si existeix
       $TimeO = $this->em->getRepository('AppBundle:TimeO')
         ->findOneBy(array(
          'data' => $today,
       ));
       // si no existeix en creem una i la guardem
       if(!$TimeO){
        $TimeO = new TimeO();
        $this->em->persist($TimeO);
       }
    
    $resultat->setTime($TimeO);

    // finalitzem i guardem el resultat
    $this->get('of.manager')->saveResultat($resultat);

    // les variables utilitzades per el frontend
    $maquina = $resultat->getMaquina();
    $mesures = $resultat->getMesures();

    return $this->render('AppBundle:include:resultat.html.twig',array(
      "resultat"=>$resultat,
      "mesures" => $mesures, 
      "OF" => $OF,
      "maquina" => $maquina,
    )); 
  }


/**
 
 Editar una OF

*/
     public function newOfAction(OF $OF, Request $request)
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
          return $this->redirect($this
            ->generateUrl('admin_tests'
              ), 301
          );

        }

        return $this->render(
        	'AppBundle:content:new_OF.html.twig',array(
          'form' => $form->createView(), 
        ));
    }

/**
 
 vista genral d'una OF

*/
  public function iniTestAction(Of $OF ,Request $request)
  {   
    $this->controllerIni();
    // recuperamos la linia testeada
    $linia = $OF->getLinia(); 

    return $this->render(
      'AppBundle:content:OF_overview.html.twig',
    array(
      // variables del frontend
      'OF' => $OF,
      'linia' => $linia,
    ));
  }




  /**
 
 Creem un nou resultat (new test vision)

*/
public function newTestAction(Request $request){
  $this->controllerIni();
  $today = date('d-m-Y');

  // recuperem la TimeO d'avui, si existeix
       $TimeO = $this->em->getRepository('AppBundle:TimeO')
         ->findOneBy(array(
          'data' => $today,
       ));
       // si no existeix en creem una i la guardem
       if(!$TimeO){
        $TimeO = new TimeO();
        $this->em->persist($TimeO);
       }
      


  $OF_id = $request->request->get('OF_id');
  $maquina_id = $request->request->get('maquina_id');

  $OF = $this->repositoris['OF']->findOneById($OF_id);
  $Maquina = $this->repositoris['Maquina']->findOneById($maquina_id);

  $resultat = $this->get('of.manager')->newResultat($OF,$Maquina);

  // li associem el TimeO
      $resultat->setTime($TimeO);
      $this->em->persist($resultat);

  return $this->testAjaxAction($resultat->getId(),$request);
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
    $mesures = $resultat->getMesures();
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
    $this->page_vars['mesures'] = $mesures;

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
    'AppBundle:content:test_overview.html.twig',
    $this->page_vars
  ); 
}

/**

  test AJAX : carreguem un resultat (render_result_ajax)

*/
public function testAjaxAction($resultat_id = false, Request $request){
    $this->controllerIni();
    // el resultat a renderitzar
    if(!$resultat_id){
      // llamada ajax
      $resultat_id = $request->request->get('resultat');
    }
    

    $resultat = $this->repositoris['Resultat']->findOneById($resultat_id);
    
    $Test = $resultat->getTest();
    $OF = $Test->getOf();
    $maquina = $resultat->getMaquina();
    $mesures = $resultat->getMesures();
  

    $page_to_render = '';
    $page_vars = array();
    $page_vars['resultat'] = $resultat;
    $page_vars['mesures'] = $mesures;

    if($resultat->getDone()){
      $page_vars['OF'] = $OF;
      $page_vars['maquina'] = $maquina;
      $page_to_render = 'AppBundle:include:resultat.html.twig';

    }
    else{
      $Pes = new Pes();
      $mesuraForm = $this->createForm(new NewMesuraType(), $Pes);  
      $resultParamsForm = $this->createForm(new ResultParamsType(), $resultat);

      $page_to_render = 'AppBundle:include:test.html.twig'; 
      $page_vars['resultParamsForm'] = $resultParamsForm->createView(); 
      $page_vars['mesuraForm'] = $mesuraForm->createView();
    }
  
  return $this->render($page_to_render, $page_vars); 

}



/**
 
 Reobrim un resultat 

*/
  public function reopenResultAction(Resultat $resultat){
    $this->controllerIni();
    $today = date('d-m-Y');
    
    if (!$resultat) {
      throw $this->createNotFoundException("El resultat no s'ha trobat");
    }

    // recuperem la TimeO d'avui, si existeix
       $TimeO = $this->em->getRepository('AppBundle:TimeO')
         ->findOneBy(array(
          'data' => $today,
       ));
       // si no existeix en creem una i la guardem
       if(!$TimeO){
        $TimeO = new TimeO();
        $this->em->persist($TimeO);
       }

    // reobrim el test
    $resultat->setDone(false);

    $resultat->setTime($TimeO);

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
    $this->controllerIni();

    if(is_object($this->process)){$this->process->stop();}

    $respuesta = '';
      $this->process = new Process('python satel.py');
      $this->process->setTimeout(60);
      $this->process->run();
    
    while ($this->process->isRunning()) {
    // waiting for process to finish
    }

    if (!$this->process->isSuccessful()) {
      //$respuesta = $process->getErrorOutput();
      $respuesta = 'finish_process';
      $this->process->stop();
      return new Response($respuesta,200);
    }
    $respuesta = $this->process->getOutput();
    return new Response($respuesta,200);
}

/**
 
 Borrar un resultat

*/
  public function deleteResultAction(Request $request){
    
    $this->controllerIni();
    // recuperamos las variables POST
    $resultat_id = $request->request->get('resultat_id');
    // recuperamos los objetos
    $resultat = $this->repositoris['Resultat']->findOneById($resultat_id);
    // les mesures del resultat
    
    $mesures = $resultat->getMesures();
    
   

    foreach($mesures as $mesura)
    {
      $this->em->remove($mesura);
    }

    $this->em->remove($resultat);
    $this->em->flush();

    // recuperamos las variables POST
    $OF_id = $request->request->get('id');
    $maquina_id = $request->request->get('maquina_id');
    // recuperamos los objetos
    $maquina = $this->repositoris['Maquina']->findOneById($maquina_id);
    $OF = $this->repositoris['OF']->findOneById($OF_id);
    $test = $OF->getTest();
    // recuperem tots els resultats de la màquina dins el test actual
    $resultats = $this->get('of.manager')->getAllResultats($test->getId(),$maquina_id);

    return $this->render(
      'AppBundle:include:resultats_list.html.twig',array(
        'OF' => $OF,
        'maquina' => $maquina,
        'resultats' => $resultats,
      ));
  }


/**
 
 Borrar una mesura

*/
  public function deleteMesuraAction(Request $request){
    
    $this->controllerIni();
    // recuperamos las variables POST
    $mesura_id = $request->request->get('mesura_id');
    $OF_id = $request->request->get('OF_id');
    // recuperamos los objetos
    $mesura = $this->repositoris['Mesura']->findOneById($mesura_id);
    // les mesures del resultat
    $this->em->remove($mesura);
    $this->em->flush();
    
    /**
    falta generalitzar
    */
    $resultat_id = $request->request->get('resultat_id');
    $resultat = $this->repositoris['Resultat']->findOneById($resultat_id);
    
    $Test = $resultat->getTest();
    $OF = $Test->getOf();
    $maquina = $resultat->getMaquina();
    $mesures = $resultat->getMesures();
  
    $page_to_render = '';
    $page_vars = array();
    $page_vars['resultat'] = $resultat;
    $page_vars['mesures'] = $mesures;

    if($resultat->getDone()){
      $page_vars['OF'] = $OF;
      $page_vars['maquina'] = $maquina;
      $page_to_render = 'AppBundle:include:resultat.html.twig';
    }
    else{
      $Pes = new Pes();
      $mesuraForm = $this->createForm(new NewMesuraType(), $Pes);  
      $resultParamsForm = $this->createForm(new ResultParamsType(), $resultat);

      $page_to_render = 'AppBundle:include:test.html.twig'; 
      $page_vars['resultParamsForm'] = $resultParamsForm->createView(); 
      $page_vars['mesuraForm'] = $mesuraForm->createView();
    }

  return $this->render($page_to_render, $page_vars);
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
    return $this->render('AppBundle:content:admin_tests.html.twig',array(
      'form'=>$form->createView(), 
    ));
  }

/**
 
 Visualitzar una OF finalitzada

*/
  public function viewOfAction($OF_id){
    $page_vars = array();
    
    

    $page_vars['prueba'] = 'hola' ;

    return $this
        ->render(
          'AppBundle:content:OF_report.html.twig',
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
          'AppBundle:content:OF_report.html.twig',
           $page_vars
           );
  }


  /**
 
 Report d'una maquina

*/  
  public function maquinaReportAction(OF $OF, Maquina $maquina, Request $request)    
  {


  }


/**

 Carrega la llista de OFs sense finalitzar

*/
public function list_OF_pendentsAction() 
  {
    // recuperem les OF sense finalitzar
    $OF_list = $this->get('of.manager')->getUnDoneOf();

    return $this->render(
      'AppBundle:ajax:list_OF_pendents.html.twig',array(
      "OF_list" => $OF_list,
    ));
}


/**

 Borra una OF no finalitzada

*/
public function deleteOFAction($OF) 
  {
    // borrem la OF sense finalitzar
    $this->get('of.manager')->removeOf($OF);

    // recuperem les OF sense finalitzar
    $OF_list = $this->get('of.manager')->getUnDoneOf();

    return $this->redirect($this
            ->generateUrl('admin_tests',array(
              'OF' => $OF
              )), 301
          );
}


/**
 
  (AjaX) enviem la llista dels resultats per a una màquina dins un test

*/
  public function listResultatsAction(Request $request)
  {   
    $this->controllerIni();
    // recuperamos las variables POST
    $OF_id = $request->request->get('id');
    $maquina_id = $request->request->get('maquina_id');
    // recuperamos los objetos
    $maquina = $this->repositoris['Maquina']->findOneById($maquina_id);
    $OF = $this->repositoris['OF']->findOneById($OF_id);
    $test = $OF->getTest();
    // recuperem tots els resultats de la màquina dins el test actual
    $resultats = $this->get('of.manager')->getAllResultats($test->getId(),$maquina_id);

    return $this->render(
      'AppBundle:include:resultats_list.html.twig',array(
        'OF' => $OF,
        'maquina' => $maquina,
        'resultats' => $resultats,
      ));
  }
  
/**
 
  Pàgina dels tests realitzats ordenats per data

*/
  public function resultatsResumAction()
  {
   $this->controllerIni();
   // recuperem totes les dates a mostrar
   $dates = $this->repositoris['TimeO']->findAll();
   $Days = new Days();
   $months = array();
   $months['juny'] = $Days->getDaysOfMonth('juny');
   $months['juliol'] = $Days->getDaysOfMonth('juliol');
   $months['agost'] = $Days->getDaysOfMonth('agost');
   
   // recorrem les dates on hi han tests
   foreach($dates as $data)
   {
    // capturem la data del test
    $la_data = explode('-',$data->getData());
    $dia = $la_data[0];
    $mes = $la_data[1]==6?'juny':'juliol';
    $any = $la_data[2];
    // comuniquem al calendari que en aquells data hi ha un test
    $months[$mes][$dia] = $data->getId();
   }
  $families = $this->repositoris['Familia']->findAll();

  return $this->render(
      'AppBundle:content:resultats_resum.html.twig',array(
      'dates'=>$dates,
      'months'=>$months,
      'families'=>$families,
      ));
  }

  

/**
 
  (AjaX) enviem la llista dels tests per a una data particular

*/

  public function getDataTestsAction(Request $request)
  {

     $this->controllerIni();
     $tests_list = array();
     $tests_list_cache = array();

    $timeo_id = $request->request->get('timeo');
    $tipus = $request->request->get('tipus');

    

    if($timeo_id){
      // recuperem l'objecte TimeO
      $TimeO = $this->repositoris['TimeO']->findOneById($timeo_id);
      // recuperem els tests associats
      $tests = $this->repositoris['Resultat']->findBy(array(
        "time"=>$timeo_id,)
      );
      
      $lasttest = '';

      foreach($tests as $test)
      {
        //if($test->getDone()){
          $tests_list_cache[] = $test;

        //}
      }
      

      if($tipus){
        if($tipus == 'all'){

          $tests_list = $tests_list_cache;
        }
        else{

          foreach($tests_list_cache as $test)
          {
            $maquina_test = $test->getMaquina();
            $familia_test = $maquina_test->getFamilia();
            $tipus_test = $familia_test->getTipus();

            if($tipus_test == $tipus){
              $tests_list[] = $test;
            }
        }// foreach
      } // else
    }// if tipus
    else{
      $tests_list = $tests_list_cache;
    }
  } // if timeo
    
 
    if($tipus && !$timeo_id){
      if($tipus == 'all'){
        $tests_list = $this->repositoris['Resultat']->findBy(array(
          'done'=>1));
      } // if all
      else{
      
      $families = $this->repositoris['Familia']->findBy(array(
        'tipus'=>$tipus));

     

      foreach($families as $familia){
       
 
          $maquines = $familia->getMaquines();

          foreach($maquines as $maquina){
            $maquina_id = $maquina->getId();
            $maquina_tests = $this->repositoris['Resultat']->findBy(array(
              'maquina' => $maquina_id,
              'done'=>1,
              ), array(
              'data'=>'DESC',
              )
            );

            foreach($maquina_tests as $maquina_test){
               $tests_list[] = $maquina_test;
            } // foreach3
          } // foreach 2
      
      } // foreach 1
    } // else all
    } // if tipus 
    //return new Response($TimeO->getId(),200);

    return $this->render(
      'AppBundle:include:resultats_data.html.twig',array(
        'tests_list'=>$tests_list,
      ));

  }
}
