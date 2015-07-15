<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// utils
use Symfony\Component\HttpFoundation\Request;




class BaseController extends Controller
{

  protected $repositoris;
  protected $em;

/**
 Constructor 
*/
    public function controllerIni(){ 

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
  }
}
