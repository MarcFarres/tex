<?php  

namespace AppBundle\Constant;
 
final class Days
{
    /**
     * Nombres de los eventos que lanzan los usuarios
     * al interactuar con el sistema
     *
     * 
     */
    // carpeta donde se guardan las imágenes de productos, familias, ...
    protected $months;

    public function __construct(){
    	$junyDays = array();
    	for($i=1;$i<=30;$i++)
    	{
          $junyDays[$i] = $i<10?"2015-06-0".$i:"2015-06-".$i;
    	}
    	$juliolDays = array();
    	for($i=1;$i<=31;$i++)
    	{
          $juliolDays[$i] = $i<10?"2015-07-0".$i:"2015-07-".$i;
    	}
    	$agostDays = array();
    	for($i=1;$i<=31;$i++)
    	{
          $agostDays[$i] = $i<10?"2015-08-0".$i:"2015-08-".$i;
    	}
      $setembreDays = array();
      for($i=1;$i<=30;$i++) 
      {
          $setembreDays[$i] = $i<10?"2015-09-0".$i:"2015-09-".$i;
      }

    	$this->months['juny'] = $junyDays;
    	$this->months['juliol'] = $juliolDays;
    	$this->months['agost'] = $agostDays;
      $this->months['setembre'] = $setembreDays;
    }

    public function getDaysOfMonth($month)
    {
      if(isset($this->months[$month]))return $this->months[$month];
      else return 'El mes no existe en la base de datos';
    }
    
}