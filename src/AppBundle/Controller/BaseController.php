<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// utils
use Symfony\Component\HttpFoundation\Request;




class BaseController extends Controller
{

/**
  Visió global de les màquines
*/
    public function CreatorFormAction()
    {
      
    	$linia_repo = $this->getDoctrine()->getRepository('AppBundle:Linia');
        
        $linias = $linia_repo->findAll();

        return $this->render(
          'AppBundle:content:index.html.twig',
          array(
            'linias' => $linias
          )
        );
    }

}
