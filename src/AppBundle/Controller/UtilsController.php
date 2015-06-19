<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Process\Process;

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



class UtilsController extends Controller
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

    $this->repositoris['Pes'] = $doctrine
      ->getRepository('AppBundle:Pes');

    $this->repositoris['Familia'] = $doctrine
      ->getRepository('AppBundle:Familia');

    $this->repositoris['TimeO'] = $doctrine
      ->getRepository('AppBundle:TimeO');

  }


}
