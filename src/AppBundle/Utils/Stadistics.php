<?php 

namespace AppBundle\Utils ;

class Stadistics extends CommonMaths
{
  


  /* ===============================================
    Restorna el '$percent' percent del número '$num' 
  ------------------------------------------------*/
  public function percent($percent,$num){

  }

  /* ==============================================
    Calcula la mitjana estadística d'un conjunt 
    de valors
  ------------------------------------------------*/
  public function average($values)
  {

  	$count = 0;
  	$result = 0;
  	$sum = 0;

    foreach($values as $value){
    
      $sum+=$value;
      $count++;
    
    }// end foreach
    if($count!=0){
      $result = $sum/$count ;
    }else{
      $result = 0 ;
    }

    return $result;

  }
  
 /* ==============================================
    Calcula la desviació standard relativa d'un 
    conunt de mesures
  ------------------------------------------------*/
  public function stdDev($values)
  {
    
    // Obtenim la mitjana estadística d'un conjunt
    // de mesures experimentals
    $average = $this->average($values);

    $sum = 0;
    $partial = 0;
    $count = 0;
    $result = 0;

  	foreach ($values as $value) {

      $partial = $value - $average;
      $partial = $this->square($partial);

      $sum += $partial ;
      $count++;
  		
  	}// end foreach
    
    // apliquem la fórmula de la desviació standard
    if($count!=0 && $count!=1){
      $result = $sum/($count - 1) ;
    }else{
      $result = 0;
    }
    
    $result = sqrt($result);

    return $result;

  }

  
  /* ==============================================
    Retorna el 'descentratge' relatiu (signed) d'una
    mesura respecte del valor que esperavem obtenir
    '$reference'
  ------------------------------------------------*/
  public function descRel($value,$reference)
  {
    $result = 0;
    if($reference!=0)
    {
      $result = ($value - $reference)/$reference ;
    }
     

     return $result;
  }

  /* ==============================================
    Retorna el 'descentratge' relatiu (signed) d'una
    mesura respecte del valor que esperavem obtenir
    ** funció especial per a les màquines contínues
    '$reference'
  ------------------------------------------------*/
  public function inverseDescRel($value,$reference)
  {
    $result = 0;
    if($reference!=0)
    {
       $result = ($value - $reference)/$reference ;
    }
     return $result;
  }

}