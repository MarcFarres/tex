<?php 

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="test")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TestRepository")
 */
class Test
{
	/**
	*
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/ protected $id;
    
    /**
    * @ORM\OneToOne(targetEntity="Of",mappedBy="test")
    */ protected $of;
    
    /**
    * @ORM\OneToMany(targetEntity="Resultat", mappedBy="test")
    */ protected $resultats;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->resultats = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Test
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
     * Set of
     *
     * @param \AppBundle\Entity\Of $of
     * @return Test
     */
    public function setOf(\AppBundle\Entity\Of $of = null)
    {
        $this->of = $of;

        return $this;
    }

    /**
     * Get of
     *
     * @return \AppBundle\Entity\Of 
     */
    public function getOf()
    {
        return $this->of;
    }

   
    /**
     * Add resultats
     *
     * @param \AppBundle\Entity\Resultat $resultats
     * @return Test
     */
    public function addResultat(\AppBundle\Entity\Resultat $resultats)
    {
        $this->resultats[] = $resultats;

        return $this;
    }

    /**
     * Remove resultats
     *
     * @param \AppBundle\Entity\Resultat $resultats
     */
    public function removeResultat(\AppBundle\Entity\Resultat $resultats)
    {
        $this->resultats->removeElement($resultats);
    }

    /**
     * Get resultats
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getResultats()
    {
        return $this->resultats;
    }
}
