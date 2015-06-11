<?php 

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Utils\Stadistics as Math;
// constants
use AppBundle\Constant\Status ;

/**
 * @ORM\Table(name="resultat")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ResultatRepository")
 */
class Resultat
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
    * @ORM\Column(type="integer" , nullable = true , options={"default":1})
    */ protected $status;
    
    /**
    * @ORM\ManyToOne(targetEntity="Maquina",inversedBy="resultats")
    * @ORM\JoinColumn(name="maquina" , referencedColumnName="id")
    */ protected $maquina;

    
    /**
    * @ORM\Column(type="datetime")
    */ private $data;
    
    /**
    * @ORM\OneToMany(targetEntity="Mesura",mappedBy="resultat",cascade={"persist", "remove"})
    */ protected $mesures;


    /**
    * @ORM\ManyToOne(targetEntity="Test",inversedBy="resultats")
    * @ORM\JoinColumn(name="test" , referencedColumnName="id")
    */ protected $test;

    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mesures = new \Doctrine\Common\Collections\ArrayCollection();
        $this->done = false;
        $this->test_ok = false;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set mitjana_esp
     *
     * @param float $mitjanaEsp
     * @return Resultat
     */
    public function setMitjanaEsp($mitjanaEsp)
    {
        $this->mitjana_esp = $mitjanaEsp;

        return $this;
    }

    /**
     * Get mitjana_esp
     *
     * @return float 
     */
    public function getMitjanaEsp()
    {
        return $this->mitjana_esp;
    }

    /**
     * Set mitjana
     
     * @return Resultat
     */
    public function setMitjana()
    {
        $math = new Math();

        $values = array();
        $resultat = 0;

        // obtenim les mesures
        $mesures = $this->getMesures();

        foreach ($mesures as $mesura) {

           // obtenim el valor de cada mesura
           $values[] = $mesura->getDensitat()->getValor();
           //$mesura->getValor();

        } // end foreach     
        
        $resultat = $math->average($values);

        $this->mitjana = $resultat;

        return $this;
    }

    /**
     * Get mitjana
     *
     * @return float 
     */
    public function getMitjana()
    {
        return $this->mitjana;
    }

    /**
     * Set descentratge
     
     * @return Resultat
     */
    public function setDescentratge()
    {
        
        $math = new Math();
        
        // descentratge per unitat del valor esperat ()
        $resultat = $math->descRel($this->getMitjana(),$this->getMitjanaEsp()) ;
        // en percentatge
        $resultat = $resultat*100;
        // guardem el resultat
        $this->descentratge = $resultat;

        return $this;
    }

    /**
     * Get descentratge
     *
     * @return float 
     */
    public function getDescentratge()
    {
        return $this->descentratge;
    }

    /**
     * Set dev
     
     * @return Resultat
     */
    public function setDev()
    {
        $math = new Math();

        $values = array();
        $resultat = 0;

        // obtenim les mesures
        $mesures = $this->getMesures();

        foreach ($mesures as $mesura) {

           // obtenim el valor de cada mesura
           $values[] = $mesura->getDensitat()->getValor();
           //$mesura->getValor();

        } // end foreach

        $resultat = $math->stdDev($values);
        // apliquem la fórmula del client
          $resultat = $resultat/($this->mitjana) ;
          $resultat = $resultat * 100;
        // comprovacions ...

        $this->dev = $resultat;
        
        return $this;
    }

    /**
     * Get dev
     *
     * @return float 
     */
    public function getDev()
    {
        return $this->dev;
    }

    /**
     * Set desc_max
     *
     * @param float $descMax
     * @return Resultat
     */
    public function setDescMax($descMax)
    {
        $this->desc_max = $descMax;

        return $this;
    }

    /**
     * Get desc_max
     *
     * @return float 
     */
    public function getDescMax()
    {
        return $this->desc_max;
    }

    /**
     * Set maquina
     *
     * @param \AppBundle\Entity\Maquina $maquina
     * @return Resultat
     */
    public function setMaquina(\AppBundle\Entity\Maquina $maquina = null)
    {
        $this->maquina = $maquina;

        return $this;
    }

    /**
     * Get maquina
     *
     * @return \AppBundle\Entity\Maquina 
     */
    public function getMaquina()
    {
        return $this->maquina;
    }

    
    /**
     * Set test
     *
     * @param \AppBundle\Entity\Test $test
     * @return Resultat
     */
    public function setTest(\AppBundle\Entity\Test $test = null)
    {
        $this->test = $test;

        return $this;
    }

    /**
     * Get test
     *
     * @return \AppBundle\Entity\Test 
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * Set done
     *
     * @param boolean $done
     * @return Resultat
     */
    public function setDone($done)
    {
        $this->done = $done;

        return $this;
    }

    /**
     * Get done
     *
     * @return boolean 
     */
    public function getDone()
    {
        return $this->done;
    }

    /**
     * Set test_ok
     *
     * @param boolean $testOk
     * @return Resultat
     */
    public function setTestOk($testOk)
    {
        $this->test_ok = $testOk;

        return $this;
    }

    /**
     * Get test_ok
     *
     * @return boolean 
     */
    public function getTestOk()
    {
        return $this->test_ok;
    }

    /**
     * Set dev_max
     *
     * @param float $devMax
     * @return Resultat
     */
    public function setDevMax($devMax)
    {
        $this->dev_max = $devMax;

        return $this;
    }

    /**
     * Get dev_max
     *
     * @return float 
     */
    public function getDevMax()
    {
        return $this->dev_max;
    }

    /**
     * Set longitud
     *
     * @param integer $longitud
     * @return Resultat
     */
    public function setLongitud($longitud)
    {
        $this->longitud = $longitud;

        return $this;
    }

    /**
     * Get longitud
     *
     * @return integer 
     */
    public function getLongitud()
    {
        return $this->longitud;
    }



    /**
evaluació de l'èxit del test
    */
    public function evaluate()
    {
      $math = new Math();

      // finalitzem el test
        $this->setDone(true);
      // data de finalització
        $this->setData(new \DateTime('now'));

      $desc_maxim = $this->getDescMax();
      // descentratge obtingut experimentalment
      $desc_obtingut = $this->getDescentratge();
      // en prenem el valor absolut
      $desc_obtingut = $math->absValue($desc_obtingut);

      $dev_maxim = $this->getDevMax();
      $dev_obtingut = $this->getDev();

      if($desc_obtingut > $desc_maxim){
        // test no superat amb èxit
        $this->setTestOk(false);
        $this->setStatus(4);
      }
      else{
        // test superat amb èxit
        $this->setTestOk(true);
        $this->setStatus(3);
      }
    }

   

    /**
     * Add mesures
     *
     * @param \AppBundle\Entity\Mesura $mesures
     * @return Resultat
     */
    public function addMesure(\AppBundle\Entity\Mesura $mesures)
    {
        $this->mesures[] = $mesures;

        return $this;
    }

    /**
     * Remove mesures
     */
    public function removeMesures()
    {
        //$this->mesures->removeElement($mesures);
    }


    /**
     * Remove mesura
     */
    public function removeMesura($mesura)
    {
        $this->mesures->removeElement($mesura);
    }

    /**
     * Get mesures
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMesures()
    {
        return $this->mesures;
    }

    /**
     * Set status
     * @return Resultat
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \intenger 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set data
     *
     * @param \DateTime $data
     * @return Resultat
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return \DateTime 
     */
    public function getData()
    {
        return $this->data;
    }
}
