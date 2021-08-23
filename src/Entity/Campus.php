<?php

namespace App\Entity;

use App\Repository\CampusRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CampusRepository::class)
 */
class Campus
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\OneToOne(targetEntity=Participant::class, mappedBy="idCampus", cascade={"persist", "remove"})
     */
    private $idParticipant;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getIdParticipant(): ?Participant
    {
        return $this->idParticipant;
    }

    public function setIdParticipant(Participant $idParticipant): self
    {
        // set the owning side of the relation if necessary
        if ($idParticipant->getIdCampus() !== $this) {
            $idParticipant->setIdCampus($this);
        }

        $this->idParticipant = $idParticipant;

        return $this;
    }
}
