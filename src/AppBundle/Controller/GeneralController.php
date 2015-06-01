<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// Entities
use AppBundle\Entity\Linia;
use AppBundle\Entity\Sublinia;
use AppBundle\Entity\Familia;
use AppBundle\Entity\Maquina;

// formularis
use AppBundle\Form\Type\NewLiniaType ;
use AppBundle\Form\Type\NewSubliniaType ;
use AppBundle\Form\Type\NewFamiliaType ;
use AppBundle\Form\Type\NewMaquinaType ;
use AppBundle\Form\Type\MaquinaToSubliniaType ;


// utils
use Symfony\Component\HttpFoundation\Request;




class GeneralController extends Controller
{

/**
  Visió global de les màquines
*/
    public function indexAction(Request $request)
    {
    	$linia_repo = $this->getDoctrine()->getRepository('AppBundle:Linia');
        
        $linias = $linia_repo->findAll();


        return $this->render(
          'AppBundle:content:index.html.twig',
          array(
            'linias' => $linias,
          )
        );
    }

/**
  creació d'una nova linia
*/
     public function newLiniaAction(Request $request)
    {

      $Linia = new Linia();
      $form = $this->createForm(new NewLiniaType(), $Linia);


      // comprovem si el formulari ja ha sigut enviat
      // -----------------------------------------------------------
      $form->handleRequest($request);

        if ($form->isValid()) { 
        // si el formulari es vàlid el guardem a la base de dades

          $em = $this->getDoctrine()->getManager();
          $em->persist($Linia);
          $em->flush();

        }
        return $this->render(
        	'AppBundle:content:new_Linia.html.twig',
        	array(
            'form' => $form->createView(), 
        ));
    }


/**
  creació d'una nova sublinia
*/
     public function newSubliniaAction($id,Request $request)
    {
      $Sublinia = '';

      if($id){
      	$repo = $this->getDoctrine()->getRepository('AppBundle:Sublinia');
        $Sublinia = $repo->findOneById($id); 
      }
      else{
      	$Sublinia = new Sublinia(); 
      }
      
      $form = $this->createForm(new NewSubliniaType(), $Sublinia);


      // comprovem si el formulari ja ha sigut enviat
      // -----------------------------------------------------------
      $form->handleRequest($request);

        if ($form->isValid()) { 
        // si el formulari es vàlid el guardem a la base de dades

          $em = $this->getDoctrine()->getManager();
          $em->persist($Sublinia);
          $em->flush();

        }
        return $this->render(
        	'AppBundle:content:new_Sublinia.html.twig',
        	array(
            'form' => $form->createView(), 
        ));
    }


/**
  creació d'una nova maquina
*/
     public function newMaquinaAction(Request $request)
    {

      $Maquina = new Maquina();
      $form = $this->createForm(new NewMaquinaType(), $Maquina);


      // comprovem si el formulari ja ha sigut enviat
      // -----------------------------------------------------------
      $form->handleRequest($request);

        if ($form->isValid()) { 
        // si el formulari es vàlid el guardem a la base de dades

          $em = $this->getDoctrine()->getManager();
          $em->persist($Maquina);
          $em->flush();

        }
        return $this->render(
        	'AppBundle:content:new_Maquina.html.twig',
        	array(
            'form' => $form->createView(), 
        ));
    }

/**
  Afegir una maquina a una sublinia
*/
     public function addMaquinaAction(Request $request,$id)
    {
      
      $sublinia_repo = $this->getDoctrine()->getRepository('AppBundle:Sublinia');
      $Sublinia = $sublinia_repo->findById($id);


      $form = $this->createForm(new MaquinaToSubliniaType(), $Sublinia);


      // comprovem si el formulari ja ha sigut enviat
      // -----------------------------------------------------------
      $form->handleRequest($request);

        if ($form->isValid()) { 
        // si el formulari es vàlid el guardem a la base de dades

          $em = $this->getDoctrine()->getManager();
          $em->persist($Sublinia);
          $em->flush();

        }
        return $this->render(
        	'AppBundle:content:add_Maquina.html.twig',
        	array(
            'form' => $form->createView(), 
        ));
    }



/**
  creacio d'una nova familia de maquines
*/
     public function newFamiliaAction(Request $request)
    {

      $Familia = new Familia();
      $form = $this->createForm(new NewFamiliaType(), $Familia);


      // comprovem si el formulari ja ha sigut enviat
      // -----------------------------------------------------------
      $form->handleRequest($request);

        if ($form->isValid()) { 
        // si el formulari es vàlid el guardem a la base de dades

          $em = $this->getDoctrine()->getManager();
          $em->persist($Familia);
          $em->flush();

        }
        return $this->render(
        	'AppBundle:content:new_Familia.html.twig',
        	array(
            'form' => $form->createView(), 
        ));
    }

}
