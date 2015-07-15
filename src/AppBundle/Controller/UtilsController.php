<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Controller\BaseController;

// Entities
use AppBundle\Entity\Of;
use AppBundle\Entity\Test;
use AppBundle\Entity\Linia;
use AppBundle\Entity\Resultat;
use AppBundle\Entity\Mesura;
use AppBundle\Entity\Pes;
use AppBundle\Entity\Densitat;


// utils
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// constants
use AppBundle\Constant\Status ;
use AppBundle\Constant\Days;


class UtilsController extends BaseController
{
/**
  calendario de una semana
*/
  public function lastWeekAction()
  {
    $today = date('Y-m-d');
    $days = array();
    $days[] = $today;
    $days[] = date( "Y-m-d", strtotime( "-1 day", strtotime( $today ) ) );
    $days[] = date( "Y-m-d", strtotime( "-2 day", strtotime( $today ) ) );
    $days[] = date( "Y-m-d", strtotime( "-3 day", strtotime( $today ) ) );
    $days[] = date( "Y-m-d", strtotime( "-4 day", strtotime( $today ) ) );
    $days[] = date( "Y-m-d", strtotime( "-5 day", strtotime( $today ) ) );
    $days[] = date( "Y-m-d", strtotime( "-6 day", strtotime( $today ) ) );
    $days[] = date( "Y-m-d", strtotime( "-7 day", strtotime( $today ) ) );

    return $this->render(
      'AppBundle:utils:last_week.html.twig',array(
      'days'=>$days));
  }
  
/**
  pestaña para seleccionar OF's
*/
  public function ofSelectorAction($OF_list_id){
    $this->controllerIni();
    $OF_list = $this->repositoris['OF']->findAll();
    //$OF_list = $this->get('of.manager')->getUnDoneOf();

    return $this->render(
      'AppBundle:utils:of_selector.html.twig',array(
      'of_list'=>$OF_list,
      'OF_list_id'=>$OF_list_id));
  }
/**
  calendario completo
*/
public function calendarAction(){
   $Days = new Days();
   $months = array();
   $months['juny'] = $Days->getDaysOfMonth('juny');
   $months['juliol'] = $Days->getDaysOfMonth('juliol');
   $months['agost'] = $Days->getDaysOfMonth('agost');
   $months['setembre'] = $Days->getDaysOfMonth('setembre');

    return $this->render(
      'AppBundle:utils:calendari.html.twig',array(
      'months'=>$months,
      ));
  }

}
