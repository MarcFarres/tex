<?php  

namespace AppBundle\Constant;
 
final class Status
{
	
    /**
 > Resultado Status
    */
    // finalizado con éxito
    const FINALIZED_OK = 3;
    // finalizado con algún error
    const FINALIZED_FAIL = 4;
    // iniciado
    const INITIALIZED = 2;
    // sin ningún test iniciado
    const UNINITIALIZED = 1;
}