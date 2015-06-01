<?php 

namespace AppBundle\Utils ;

class CommonMaths
{

  /* ===============================================
     
  ------------------------------------------------*/
  public function pow($num,$pow){
  
  }

  /* ===============================================
     Devuelve el cuadrado de un valor '$num'
  ------------------------------------------------*/
  public function square($num){
    
    return $num*$num ;
  }


  /* ==============================================
    Retorna el valor absolut d'una magnitud
  ------------------------------------------------*/
  public function absValue($value)
  {
    
   $result = ($value<0?(-1)*$value:$value);

   return $result;

  }
}