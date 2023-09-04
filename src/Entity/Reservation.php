<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;


/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 */

class Reservation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     */
    private $end;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;
    
    /**
     * @ORM\Column(name="resourceId", type="string", length=255)
     */
    private $resourceId;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="Instructeur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="instructeur", referencedColumnName="id")
     * })
     */
    private $instructeur;

    /**
    * @var \User
    *
    * @ORM\ManyToOne(targetEntity="User")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="User_id", referencedColumnName="id")
    * })
    */
    private $user;    
    
    /**
     * @ORM\Column(name="reservataire", type="integer")
     */
    private $reservataire;

     /**
     * @ORM\Column(name="appareil", type="string", length=255)
     */
    private $appareil; 

    /**
     * @ORM\Column(name="formateur", type="string", length=255)
     */
    private $formateur;

    /**
    * @var \Avion
    * @ORM\ManyToOne(targetEntity="Avions")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="Avion", referencedColumnName="id")
    * })
    */
    private $avion;

//    /**
//     * @var notification
//     * @ORM\Column(name="notification", type="boolean", options={"default":false})
//     */
//    private $notification;    

//    /**
//     * @ORM\Column(name="CodeReservation", type="string", length=12)
//     */
//    private $CodeReservation;

    /**
     * @ORM\Column(name="realisation", type="boolean", options={"default":false} )
     */
    private $realisation;

    /**
     * @ORM\Column(name="NumeroOrdre",type="string", length=12)
     */
    private $NumeroOrdre;


    public function getAvion(): ?Avions
    {
        return $this->avion;
    }
    
    public function setAvion(?Avions $avion): self
    {
        $this->avion = $avion;
    
        return $this;
    }    
    
    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getResourceId(): ?string
    {
        return $this->resourceId;
    }

    public function setResourceId(string $resourceId): self
    {
        $this->resourceId = $resourceId;

        return $this;
    }

    public function DureeDuVol(){

        $dureeduvol = date_diff($this->end,$this->start);
        return $dureeduvol->format('%H:%I');
    }

    public function getInstructeur(): ?Instructeur
    {
        return $this->instructeur;
    }

    public function setInstructeur(?Instructeur $instructeur): self
    {
        $this->instructeur = $instructeur;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    } 
    
    public function getReservataire(): ?int
    {
        return $this->reservataire;
    }
    
    public function setReservataire(int $reservataire): self
    {
        $this->reservataire = $reservataire;
    
        return $this;
    }

    public function getAppareil(): ?string
    {
        return $this->appareil;
    }

    public function setAppareil(string $appareil): self
    {
        $this->appareil = $appareil;

        return $this;
    }    

    public function getFormateur(): ?string
    {
        return $this->formateur;
    }

    public function setFormateur(string $formateur): self
    {
        $this->formateur = $formateur;

        return $this;
    }

    public function toString($object): string
    {

        return $this->NumeroOrdre ;        
        return $object instanceof Reservation
            ? $object->getTitle()
            : 'Reservation'; // shown in the breadcrumb on the create view
    }

    //public function getCodeReservation(): ?string
    //{
    //    return $this->CodeReservation;
    //}

    //public function setCodeReservation(string $CodeReservation): self
    //{
    //    $this->CodeReservation = $CodeReservation;

    //    return $this;
    //}

    public function __toString(): string
    {
		  //return $this->facture;
          return $this->NumeroOrdre ;
	}

    public function isRealisation(): ?bool
    {
        return $this->realisation;
    }

    public function setRealisation(bool $realisation): self
    {
        $this->realisation = $realisation;

        return $this;
    }

    public function getNumeroOrdre(): ?string
    {
        return $this->NumeroOrdre;
    }

    public function setNumeroOrdre(string $NumeroOrdre): self
    {
        $this->NumeroOrdre = $NumeroOrdre;

        return $this;
    }
  
}
