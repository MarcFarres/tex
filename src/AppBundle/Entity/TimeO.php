<?php 

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="timeo")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TimeORepository")
 */
class TimeO
{
  /**
  * @ORM\Column(type="integer")
  * @ORM\Id
  * @ORM\GeneratedValue(strategy="AUTO")
  */ protected $id;
  
  /**
  * @ORM\Column(type="string")
  */ protected $data;
  
  /**
  * Bidirectional -  OWNING SIDE
  * @ORM\OneToMany(targetEntity="Resultat",mappedBy="time")
  */ protected $resultat;

    
    /**
     * Constructor
     */
    public function __construct()
    {
        $today = new \DateTime();
        //$today = $today->format('d-m-Y');
        $this->data = date('d-m-Y');
        $this->resultat = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set data
     *
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Add resultat
     *
     * @param \AppBundle\Entity\Resultat $resultat
     * @return TimeO
     */
    public function addResultat(\AppBundle\Entity\Resultat $resultat)
    {
        $this->resultat[] = $resultat;

        return $this;
    }

    /**
     * Remove resultat
     *
     * @param \AppBundle\Entity\Resultat $resultat
     */
    public function removeResultat(\AppBundle\Entity\Resultat $resultat)
    {
        $this->resultat->removeElement($resultat);
    }

    /**
     * Get resultat
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getResultat()
    {
        return $this->resultat;
    }
}
