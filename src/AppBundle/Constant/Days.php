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
          $junyDays[$i] = false;
    	}
    	$juliolDays = array();
    	for($i=1;$i<=31;$i++)
    	{
          $juliolDays[$i] = false;
    	}
    	$agostDays = array();
    	for($i=1;$i<=31;$i++)
    	{
          $agostDays[$i] = false;
    	}

    	$this->months['juny'] = $junyDays;
    	$this->months['juliol'] = $juliolDays;
    	$this->months['agost'] = $agostDays;
    }

    public function getDaysOfMonth($month)
    {
      if(isset($this->months[$month]))return $this->months[$month];
      else return 'El mes no existe en la base de datos';
    }
    
}