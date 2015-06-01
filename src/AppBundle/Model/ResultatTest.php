<?php 

namespace AppBundle\Model;

use AppBundle\Utils\Stadistics as Math;
use Doctrine\ORM\Mapping as ORM;


class ResultatTest
{
  
  /**
    * @ORM\Column(type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */ protected $id;

    /**
    * @ORM\Column(type="float" , nullable = true)
    */ protected $mitjana_esp;

    /**
    * @ORM\Column(type="float" , nullable = true)
    */ protected $mitjana;

    /**
    * @ORM\Column(type="float" , nullable = true)
    */ protected $descentratge;

    /**
    * @ORM\Column(type="float" , nullable = true)
    */ protected $dev;

    /**
    * @ORM\Column(type="float" , nullable = true)
    */ protected $dev_max;

    /**
    * @ORM\Column(type="integer" , nullable = true)
    */ protected $longitud;

    /**
    * @ORM\Column(type="float" , nullable = true)
    */ protected $desc_max;

    /**
    * @ORM\Column(type="boolean" , options={"default":false})
    */ protected $done;

    /**
    * @ORM\Column(type="boolean" , options={"default":false})
    */ protected $test_ok;
    
    /**
    * @ORM\ManyToOne(targetEntity="Maquina",inversedBy="tests")
    * @ORM\JoinColumn(name="maquina" , referencedColumnName="id")
    */ protected $maquina;


    /**
    * @ORM\OneToMany(targetEntity="Mesura",mappedBy="resultat")
    */ protected $mesures;


    /**
    * @ORM\ManyToOne(targetEntity="Test",inversedBy="tests")
    * @ORM\JoinColumn(name="test" , referencedColumnName="id")
    */ protected $test;
    
    /**
    * Objeto que realiza las operaciones matemáticas necesarias
    */  private $math;


  /**
     * Constructor
     */
    public function __construct()
    {
        $this->mesures = new \Doctrine\Common\Collections\ArrayCollection();
        $this->done = false;
        $this->test_ok = false;

        $this->math = new Math();
    }


  public function getDesc()
  {
     

  }

  public function getDev()
  {
     
  }

  public function setAverage()
  {
     
     
  }

}
