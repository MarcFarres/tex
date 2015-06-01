<?php 

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="sublinia")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SubliniaRepository")
 */
class Sublinia
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
    * Bidirectional    (OWNING SIDE) 
    * @ORM\ManyToOne(targetEntity="Linia",inversedBy="sublinies")
    * @ORM\JoinColumn(name="linia_id",referencedColumnName="id")
    **/ protected $linia;

    /**
    * Unidirectional    (OWNING SIDE) 
    * @ORM\ManyToOne(targetEntity="Familia")
    * @ORM\JoinColumn(name="familia_id",referencedColumnName="id")
    **/ protected $familia;

    /**
    * BiDirectional    (Reverse Side) 
    * @ORM\OneToMany(targetEntity="Maquina" , mappedBy="sublinia")
    **/ protected $maquines;
    
    /**
    *@ORM\Column(type="float",nullable=true)
    */ protected $mitjana_esp;

    /**
    *@ORM\Column(type="float",nullable=true)
    */ protected $descentratge;

    /**
    *@ORM\Column(type="float",nullable=true)
    */ protected $desv;

    /**
    *@ORM\Column(type="integer",nullable=true)
    */ protected $longitud;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->maquines = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Sublinia
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
     * @return Sublinia
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
     * Set linia
     *
     * @param \AppBundle\Entity\Linia $linia
     * @return Sublinia
     */
    public function setLinia(\AppBundle\Entity\Linia $linia = null)
    {
        $this->linia = $linia;

        return $this;
    }

    /**
     * Get linia
     *
     * @return \AppBundle\Entity\Linia 
     */
    public function getLinia()
    {
        return $this->linia;
    }

    /**
     * Set familia
     *
     * @param \AppBundle\Entity\Familia $familia
     * @return Sublinia
     */
    public function setFamilia(\AppBundle\Entity\Familia $familia = null)
    {
        $this->familia = $familia;

        return $this;
    }

    /**
     * Get familia
     *
     * @return \AppBundle\Entity\Familia 
     */
    public function getFamilia()
    {
        return $this->familia;
    }

    /**
     * Add maquines
     *
     * @param \AppBundle\Entity\Maquina $maquines
     * @return Sublinia
     */
    public function addMaquine(\AppBundle\Entity\Maquina $maquines)
    {
        $this->maquines[] = $maquines;

        return $this;
    }

    /**
     * Remove maquines
     *
     * @param \AppBundle\Entity\Maquina $maquines
     */
    public function removeMaquine(\AppBundle\Entity\Maquina $maquines)
    {
        $this->maquines->removeElement($maquines);
    }

    /**
     * Get maquines
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMaquines()
    {
        return $this->maquines;
    }

    /**
     * Set mitjana_esp
     *
     * @return Sublinia
     */
    public function setMitjanaEsp($mitjanaEsp)
    {
        $this->mitjana_esp = $mitjanaEsp;

        return $this;
    }

    /**
     * Get mitjana_esp
     *
     */
    public function getMitjanaEsp()
    {
        return $this->mitjana_esp;
    }

    
   
    /**
     * Set desv
     *
     * @return Sublinia
     */
    public function setDesv($desv)
    {
        $this->desv = $desv;

        return $this;
    }

    /**
     * Get desv
     */
    public function getDesv()
    {
        return $this->desv;
    }

    /**
     * Set descentratge
     *
     * @return Sublinia
     */
    public function setDescentratge($descentratge)
    {
        $this->descentratge = $descentratge;

        return $this;
    }

    /**
     * Get descentratge
     *
     */
    public function getDescentratge()
    {
        return $this->descentratge;
    }

    /**
     * Set longitud
     *
     * @param integer $longitud
     * @return Sublinia
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
}
