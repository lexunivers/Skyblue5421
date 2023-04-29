<?php

namespace App\Entity;

use App\Repository\ParametresRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

//#[ORM\Entity(repositoryClass: ParametresRepository::class)]

/**
 * @ORM\Table(name="parametres", indexes={@ORM\Index(name="clef", columns={"clef"})})
 * @ORM\Entity(repositoryClass=ParametresRepository::class)
 */

class Parametres
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;



    
	/**
     * @var string
     * @ORM\Column(name="clef", type="string", length=255, nullable=false)
     */

    private $clef;


//    #[ORM\Column(type: Types::TEXT)]
    
	/**
     * @var string
     * @ORM\Column(name="valeur", type="string", length=255, nullable=false)
     */
    private $valeur;

    //#[ORM\Column]
    
    /**
    * @ORM\Column(type="datetime")
    */  	
	private $update_at;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClef(): ?string
    {
        return $this->clef;
    }

    public function setClef(string $clef): self
    {
        $this->cle = $clef;

        return $this;
    }
        
    public function getValeur(): ?string
    {
        return unserialize($this->valeur);
    }
     
    public function setValeur(?string $valeur): self
    {
        $this->valeur = serialize($valeur);
     
        return $this;
    }
     
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }
     
    public function setUpdatedAt(?string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
     
        return $this;
    }    
}
