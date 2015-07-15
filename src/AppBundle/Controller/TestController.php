<?php

namespace AppBundle\Controller;

use AppBundle\Controller\BaseController;

use Symfony\Component\Process\Process;

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
use AppBundle\Form\Type\assignarOfType ;

// utils
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// constants
use AppBundle\Constant\Status ;
use AppBundle\Constant\Days;


class TestController extends BaseController
{
  
  protected $page_vars;

  protected $process;

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
 
 ^get_maquines
 La llista de les màquines d'un determinat tipus
 > Ajax

*/
  public function getMaquinesAction(Request $request){
    // tipus de maquina
    $tipus = $request->request->get('tipus');
    // recuperem la llista de màquines
    $list_of_maquines = $this->get('of.manager')->get_maquines($tipus);
    
    return $this->render(
      'AppBundle:ajax:maquines_list.html.twig',array(
      'list_of_maquines'=>$list_of_maquines));
  }
/**
 
  ^resultats_list
  enviem la llista dels resultats per a una màquina dins un test
  >resultats_list_ajax (/ajax/maquines_list)

*/
  public function listResultatsAction(Request $request){   
    $this->controllerIni();
    // recuperamos las variables POST
    $OF_id = $request->request->get('id');
    $maquina_id = $request->request->get('maquina_id');
    // recuperamos los objetos
    $maquina = $this->repositoris['Maquina']->findOneById($maquina_id);
    $OF = $this->repositoris['OF']->findOneById($OF_id);
    // recuperem tots els resultats de la màquina dins el test actual
    $resultats = $this->get('test.manager')->getAllResultats($OF,$maquina_id);

    return $this->render(
      'AppBundle:ajax:resultats_list.html.twig',array(
        'OF' => $OF,
        'maquina' => $maquina,
        'resultats' => $resultats,));
  }
/**
 
 ^reopen
 Reobrim un resultat 
 >reopen_result (/tests/reopen/{resultat})

*/
  public function reopenResultAction(Resultat $resultat){
    $this->controllerIni();
    // reobrim el test
    $resultat->setDone(false);
    $this->em->persist($resultat);
    $this->em->flush();
    // el renderitzem
    return $this->redirectToRoute('render_result',array('resultat_id'=>$resultat->getId()));
  } 
/**
 
 ^render_result
 Renderitzem un resultat (test vision)
 >render_result (/tests/test/{resultat_id})

*/
  public function testAction(Resultat $resultat ,Request $request){
    $this->controllerIni();
    // el resultat a renderitzar
    $Test = $resultat->getTest();
    $OF = $Test->getOf();
    $Maquina = $resultat->getMaquina();
    $mesures = $resultat->getMesures();
    $linia = $OF->getLinia();
    // obtenim tots els resultats associats a la maquina i al test
    $resultats = $this->get('test.manager')->getAllResultats($OF,$Maquina->getId());
    // obtenim les OF sense finalitzar
    $OF_list = $this->get('of.manager')->getUnDoneOf();
    // generem les variables del frontend
    $this->page_vars['resultat'] = $resultat;$this->page_vars['OF'] = $OF;$this->page_vars['maquina'] = $Maquina;$this->page_vars['resultats'] = $resultats;$this->page_vars['OF_list'] = $OF_list;$this->page_vars['linia'] = $linia;$this->page_vars['mesures'] = $mesures;
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
    $this->page_vars); 
}
/**
 
 ^afegir_mesura
 formulari de nova mesura. Guardem la nova mesura en un resultat
 >nova_mesura_ajax (/ajax/novaMesura)

*/
  public function novaMesuraAction(Request $request)
  {
    $this->controllerIni();

    $resultat_id = $request->request->get('resultat_id');
    $valor = $request->request->get('valor');
    // el resultat a renderitzar
    $resultat = $this->repositoris['Resultat']->findOneById($resultat_id);
    // guardem la nova mesura
    $Mesura = $this->get('test.manager')->newMesura($resultat,$valor);

    return $this->render('AppBundle:include:mesura_row.html.twig',array(
        'mesura' => $Mesura, 
        'resultat' => $resultat,));
  }
/**
 
 ^finalitzar_test
 Finalitzar un resultat
 >finalitzar_resultat (/tests/finalitzar_resultat)

*/
  public function endResultatAction(Request $request){
    $this->controllerIni();
    // recuperem les variables ajax
    $resultat_id = $request->request->get('resultat_id');
    // el resultat a finalitzar
    $resultat = $this->repositoris['Resultat']->findOneById($resultat_id);
    $Test = $resultat->getTest();
    $OF = $Test->getOf();
    // finalitzem i guardem el resultat
    $this->get('test.manager')->saveResultat($resultat);
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
 
 ^delete
 Borrar un resultat
 ->delete_result (/ajax/test/delete)

*/
  public function deleteResultAction(Request $request){  
    $this->controllerIni();
    // recuperamos las variables POST
    $OF_id = $request->request->get('id');
    $maquina_id = $request->request->get('maquina_id');
    $resultat_id = $request->request->get('resultat_id');
    // recuperamos los objetos
    $resultat = $this->repositoris['Resultat']->findOneById($resultat_id);
    // les mesures del resultat
    $this->get('test.manager')->delete($resultat);
    // recuperamos los objetos
    $maquina = $this->repositoris['Maquina']->findOneById($maquina_id);
    $OF = $this->repositoris['OF']->findOneById($OF_id);
    $test = $OF->getTest();
    // recuperem tots els resultats de la màquina dins el test actual
    $resultats = $this->get('test.manager')->getAllResultats($OF,$maquina_id);

    return $this->render(
      'AppBundle:ajax:resultats_list.html.twig',array(
        'OF' => $OF,
        'maquina' => $maquina,
        'resultats' => $resultats,));
  }
/**
 
 ^new
 Creem un nou resultat (new test vision)
 >nou_resultat (/tests/new)

*/
public function newTestAction(Request $request){
  $this->controllerIni();
  // recuperem les variables ajax
  $OF_id = $request->request->get('OF_id');
  $maquina_id = $request->request->get('maquina_id');
  // recuperem la OF i la maquina
  $OF = $this->repositoris['OF']->findOneById($OF_id);
  $Maquina = $this->repositoris['Maquina']->findOneById($maquina_id);
  // creem el nou test
  $resultat = $this->get('test.manager')->newResultat($OF,$Maquina);

  return $this->testAjaxAction($resultat->getId(),$request);
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
    // en caso de error ...
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
 
 ^delete mesura
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

    return new Response('mesura borrada',200);
    
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
    public function getTestMaquinaAction(Maquina $maquina,Of $OF){
      
          $status = 'test_ok';
        
      return $this->render('AppBundle:include:maquina.html.twig',array(
          'Of' => $OF,
          'maquina' => $maquina, 
          'status' => $status,));
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
  
  ^historial
  Pàgina dels tests realitzats ordenats per data
  ->resultats_resum_inici (/tests/resultats/resum)

*/ 
  public function resultatsResumAction(){
   $this->controllerIni();
   // temporal
   // ==========================================
   
   $tests = $this->repositoris['Resultat']->findAll();
   foreach($tests as $test)
   {
     $data = $test->getData();

     $test->setDataInici($data->format('Y-m-d'));
     $test->setHora($data->format('H:i:s'));

     $this->em->persist($test);
   }
   $this->em->flush();

  $families = $this->repositoris['Familia']->findAll();

  return $this->render(
      'AppBundle:content:resultats_resum.html.twig',array(
      'families'=>$families,
      ));
  }
/**
  
  ^date
  Tests per a data i tipus de màquina
  -> get_data_tests  (/tests/resultats/tests)
  <- 'include:resultats_data'

*/
  public function getDataTestsAction(Request $request){
    $this->controllerIni();
    // recuperem la data
    $timeo = $request->request->get('timeo');
    // recuprem el tipus de màquina
    $tipus = $request->request->get('tipus');
    // preparem els arrays 
    $tests_list = array();
    $tests_list_cache = array();
    $maquines_cache = array();

    if($timeo)
    {
    // si hem seleccionat 'data' i 'tipus' de màquina alhora
      if($tipus){
        // tots els tipus
        if($tipus == 'all'){
          // agafem tots els tests de la data seleccionada
          $tests_list = $this->repositoris['Resultat']->findBy(array(
            "data_inici"=>$timeo,
          ));
        }
        else
        {
          // tests per a una data i un tipus de màquina
          $tests_list = $this->get('test.manager')->getTestsDateType($timeo,$tipus);
        }// else all 
      } // if tipus
      // només hem seleccionat una data
      else
      {
        // tots els d'una data
        $tests_list = $this->repositoris['Resultat']->findBy(array(
          "data_inici"=>$timeo
        ));
      
      } // else tipus
    } // if timeo
   
  if($tipus && !$timeo)
  {
    if($tipus == 'all')
    {
      // tots els tests disponibles
      $tests_list = $this->repositoris['Resultat']->findBy( array(), 
          array('id'=>'ASC'));
    } // if all
    else
    {
      $tests_list = $this->get('test.manager')->getTestsType($tipus);
    } // else all
  } // if tipus 
    //return new Response($TimeO->getId(),200);
  
    return $this->render(
      'AppBundle:include:resultats_data.html.twig',array(
      'tests_list'=>$tests_list,));
  }

/**
 
  vista general dels tests. Menú amb les famílies de màquines

*/
  public function visioGeneralAction(){
    $this->controllerIni();
    $families = $this->repositoris['Familia']->findAll();

    return $this->render(
      'AppBundle:content-layout:tests_overview.html.twig',array(
        'families' => $families,
        ));
  }
/**
 
  ^
  retornem les maquines d'un tipus determinat en forma de llista desordenada
  >maquines_of_type  (/ajax/maquines_of_type)

*/
  public function getMaquinesOfTipusAction(Request $request){
    $maquines_list = array();
    // recuperem la variable ajax
    $tipus = $request->request->get('tipus');
    // recuperem les màquines
    $maquines = $this->get('test.manager')->getMaquinesOfType($tipus);
    
    foreach($maquines as $maquina) {
      $linia = $maquina->getLinia();
      if($linia)
        $maquines_list[$linia->getNumero()][] = $maquina;

    }
    return $this->render(  
        'AppBundle:ajax:maquines-of-type_list.html.twig',array(
        'maquines_list' => $maquines_list,
      ));
  }

/**
 
  ^
  retornem els tests del dia d'avui d'un tipus de màquina
  -> tests_of_type  (/ajax/tests_of_type)
  <- 'ajax:tests-of-type_list.html.twig'

*/
  public function getTestsOfTipusAction(Request $request){
    $this->controllerIni();
    $tests = array();
    // recuperem la variable ajax
    $tipus = $request->request->get('tipus') != null?$request->request->get('tipus'):false;
    $data = $request->request->get('data') != null?$request->request->get('data'):false;
    $OF_id = $request->request->get('of')!= null?$request->request->get('of'):false;
    
    $info_array = array(
      'familia'=>$tipus,
      'data'=>$data,
      'maquina'=>false,
      );

    // si hem rebut una of resuperem els tests independentment de la data
    if(isset($OF_id) && $OF_id!='false' && $OF_id)
    {
      // recuperem els tests d'un tipus i una OF
    // =====================================================
      $OF = $this->repositoris['OF']->findOneById($OF_id);
      if($OF){
        $tests = $this->get('test.manager')->getTestsTypeOf($OF,$tipus);
        // info array
        $info_array['OF'] = $OF->getNumero();
      }else{ $tests = false;}
      
      return $this->render( 
        'AppBundle:ajax:tests-of-type_list.html.twig',array(
        'tests' => $tests,
        'data'=>$data,
        'tipus'=>$tipus,
        'info_array'=>$info_array,
      ));
    }
    // si no hem enviat cap data ... la data d'avui
    if(!$data){$data = date('Y-m-d');}
    // si no hem rebut la variable del tipus de maquines ...
    if(!$tipus){
      // falta el tipus !
    // =====================================================
      $tests = false;
      // info array
      $info_array['OF'] ='totes';

      return $this->render( 
        'AppBundle:ajax:tests-of-type_list.html.twig',array(
        'tests' => $tests,
        'info_array'=>$info_array,
      ));
    }
    // recuperem els tests d'un tipus i una data
    // =====================================================
    $tests = $this->get('test.manager')->getTestsDateType($data,$tipus);
    // info array
    $info_array['OF'] ='totes';

    return $this->render( 
      'AppBundle:ajax:tests-of-type_list.html.twig',array(
      'tests' => $tests,
      'info_array'=>$info_array,
    ));
  }

  /**
 
  ^nou_test
   generem un nou test per a una màquina
  -> nou_test  (/ajax/nou_test)
  <- 'ajax:tests-of-type_list.html.twig'

*/
  public function nouTestAction(Request $request){
  $this->controllerIni();
  $maquina_id = $request->request->get('maquina_id');
  $maquina = $this->repositoris['Maquina']->findOneById($maquina_id);
  $test = $this->get('test.manager')->newMaquinaResultat($maquina);
  
  $mesures = $test->getMesures();
  
   $Pes = new Pes(); 
  $mesuraForm = $this->createForm(new NewMesuraType(), $Pes); 
  
  $OF_list = $this->repositoris['OF']->findAll();

  $maquina = $test->getMaquina();
  $last_of = $maquina->getLastOf();

  return $this->render('AppBundle:ajax:test.html.twig',array(
    'test'=>$test,
    'mesures'=>$mesures,
    'mesuraForm' => $mesuraForm->createView(),
    'last_of'=>$last_of,
    'of_list'=>$OF_list,
    ));
  }
 /**
 
  ^end_test
   finalitzen un test per a una màquina, assignem la OF
  -> end_test  (/ajax/end_test)
  <- ajax:test_result.html.twig

*/
  public function endTestAction(Request $request){
  $this->controllerIni();
  // les variables ajax
  $test_id = $request->request->get('test_id');
  $Of_id = $request->request->get('of_id');
  
  $testO = $this->repositoris['Resultat']->findOneById($test_id);
  $Of = $this->repositoris['OF']->findOneById($Of_id);

  $test = $this->get('test.manager')->endTest($Of,$testO);

  return $this->render(
    'AppBundle:ajax:test_result.html.twig',array(
    'test'=>$test,
    'OF'=>$Of,
    ));
  }
 /**
 
  ^maquina_tests
   mostrem els tests per a una màquina i una data ( opcionalment per a una OF )
  -> maquina_tests  (/ajax/maquina_tests)
  <- 
*/

  public function maquinaTestsAction(Request $request){
  $this->controllerIni();
  $maquina_id = $request->request->get('maquina_id');
  $data = $request->request->get('data');


  $maquina = $this->repositoris['Maquina']->findOneById($maquina_id);
  // netegem els tests no acabats
  $this->get('test.manager')->cleanResultats();
  // resuperem els tests
  $tests = $this->get('test.manager')->getTestsDateMaquina($data,$maquina);
  // info array
  $info_array = array(
      'familia'=>$maquina->getFamilia()->getTipus(),
      'data'=>$data,
      'maquina'=>$maquina->getNumero(),
      'OF'=>'totes',
      );
  

  return $this->render('AppBundle:ajax:tests-of-type_list.html.twig',array(
    'tests'=>$tests,
    'info_array'=>$info_array,
    ));
  }


}
