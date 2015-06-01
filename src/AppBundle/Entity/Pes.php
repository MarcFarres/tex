<?php 

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="pes")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PesRepository")
 */
class Pes
{

	/**
    * @ORM\Column(type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */ protected $id;
	
    /**
    * @ORM\Column(type="float")
    */ protected $valor;


    /**
    * @ORM\Column(type="string" , length=50)
    */ protected $unitat;


    /**
    * @ORM\OneToOne(targetEntity="Mesura",mappedBy="pes")    
    */ protected $mesura;

	public function __construct()
	{   
		// les unitats per defecte són: grams (gr)
		$this->unitat = "gr";
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
     * Set valor
     *
     * @param float $valor
     * @return Pes
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return float 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set unitat
     *
     * @param string $unitat
     * @return Pes
     */
    public function setUnitat($unitat)
    {
        $this->unitat = $unitat;

        return $this;
    }

    /**
     * Get unitat
     *
     * @return string 
     */
    public function getUnitat()
    {
        return $this->unitat;
    }

    /**
     * Set mesura
     *
     * @param \AppBundle\Entity\Mesura $mesura
     * @return Pes
     */
    public function setMesura(\AppBundle\Entity\Mesura $mesura = null)
    {
        $this->mesura = $mesura;

        return $this;
    }

    /**
     * Get mesura
     *
     * @return \AppBundle\Entity\Mesura 
     */
    public function getMesura()
    {
        return $this->mesura;
    }
}
