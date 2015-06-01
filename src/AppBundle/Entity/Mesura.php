<?php 

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="mesura")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\MesuraRepository")
 */
class Mesura
{
	/**
	* @ORM\Column(type="integer")
    * @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/ protected $id;


    /**
    * @ORM\Column(type="integer")
    */ protected $numero;

    /**
    * @ORM\OneToOne(targetEntity="Pes",inversedBy="mesura")
    * @ORM\JoinColumn(name="pes",referencedColumnName="id")
    */ protected $pes;

    /**
    * @ORM\OneToOne(targetEntity="Densitat",inversedBy="mesura")
    * @ORM\JoinColumn(name="densitat",referencedColumnName="id")
    */ protected $densitat;
    

    /**
    * @ORM\ManyToOne(targetEntity="Resultat",inversedBy="mesures")
    * @ORM\JoinColumn(name="resultat_id",referencedColumnName="id")
    */ protected $resultat;

    
    public function __construct($num = 0)
    {
        $this->numero = $num;
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
     * @return Mesura
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
     * Set pes
     *
     * @param \AppBundle\Entity\Pes $pes
     * @return Mesura
     */
    public function setPes(\AppBundle\Entity\Pes $pes = null)
    {
        $this->pes = $pes;

        return $this;
    }

    /**
     * Get pes
     *
     * @return \AppBundle\Entity\Pes 
     */
    public function getPes()
    {
        return $this->pes;
    }

    /**
     * Set densitat
     *
     * @param \AppBundle\Entity\Densitat $densitat
     * @return Mesura
     */
    public function setDensitat(\AppBundle\Entity\Densitat $densitat = null)
    {
        $this->densitat = $densitat;

        return $this;
    }

    /**
     * Get densitat
     *
     * @return \AppBundle\Entity\Densitat 
     */
    public function getDensitat()
    {
        return $this->densitat;
    }

    /**
     * Set resultat
     *
     * @param \AppBundle\Entity\Resultat $resultat
     * @return Mesura
     */
    public function setResultat(\AppBundle\Entity\Resultat $resultat = null)
    {
        $this->resultat = $resultat;

        return $this;
    }

    /**
     * Get resultat
     *
     * @return \AppBundle\Entity\Resultat 
     */
    public function getResultat()
    {
        return $this->resultat;
    }
}
