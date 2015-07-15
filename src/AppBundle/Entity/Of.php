<?php 

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="of")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\OFRepository")
 */
class Of
{
	/**
	* @ORM\Column(type="integer")
    * @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/ protected $id;

    /**
	*@ORM\Column(type="integer")
	**/ protected $numero;

    /**
    *@ORM\Column(type="integer")
    **/ protected $num_partida;

    /**
    *@ORM\Column(type="string" , length=150)
    **/ protected $color;

    /**
    *@ORM\Column(type="string" , length=150)
    **/ protected $formName;

    /**
    *@ORM\Column(type="boolean" , nullable=true)
    **/ protected $done;

    /**
    *@ORM\Column(type="text")
    **/ protected $descripcio;

    /**
    * @ORM\ManyToOne(targetEntity="Linia",inversedBy="ordres")
    * @ORM\JoinColumn(name="linia_id", referencedColumnName="id")
    */ protected $linia;

    /**
     * Bidirectional - One-To-One (OWNING SIDE) 
     * @ORM\OneToOne(targetEntity="Test", inversedBy="of")
     * @ORM\JoinColumn(name="test_id" , referencedColumnName="id")
     */ private $test;
    
    /**
    * @ORM\Column(type="datetime")
    */ private $data;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tests = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Of
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set numero
     *
     * @param integer $numero
     * @return Of
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set num_partida
     *
     * @param integer $numPartida
     * @return Of
     */
    public function setNumPartida($numPartida)
    {
        $this->num_partida = $numPartida;

        return $this;
    }

    /**
     * Get num_partida
     *
     * @return integer 
     */
    public function getNumPartida()
    {
        return $this->num_partida;
    }

    /**
     * Set color
     *
     * @param string $color
     * @return Of
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string 
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set descripcio
     *
     * @param string $descripcio
     * @return Of
     */
    public function setDescripcio($descripcio)
    {
        $this->descripcio = $descripcio;

        return $this;
    }

    /**
     * Get descripcio
     *
     * @return string 
     */
    public function getDescripcio()
    {
        return $this->descripcio;
    }

    /**
     * Set data
     *
     * @param \DateTime $data
     * @return Of
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

    /**
     * Set linia
     *
     * @param \AppBundle\Entity\linia $linia
     * @return Of
     */
    public function setLinia(\AppBundle\Entity\Linia $linia = null)
    {
        $this->linia = $linia;

        return $this;
    }

    /**
     * Get linia
     *
     * @return \AppBundle\Entity\linia 
     */
    public function getLinia()
    {
        return $this->linia;
    }

    /**
     * Set test
     *
     * @param \AppBundle\Entity\Test $test
     * @return Of
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
     * @return Of
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


    public function getFormName()
    {
        return sprintf('%s - %s', $this->numero, $this->color);

    }


    /**
     * Set formName
     *
     * @param string $formName
     * @return Of
     */
    public function setFormName($formName)
    {
        $this->formName = $formName;

        return $this;
    }
}
