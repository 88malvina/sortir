<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VilleRepository::class)
 */
class Ville
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
     * @ORM\Column(type="integer")
     */
    private $cp;

    /**
     * @ORM\OneToMany(targetEntity=Lieu::class, mappedBy="ville", orphanRemoval=true)
     */
    private $lieuDeSortie;

    public function __construct()
    {
        $this->lieuDeSortie = new ArrayCollection();
    }

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

    public function getCp(): ?int
    {
        return $this->cp;
    }

    public function setCp(int $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * @return Collection|Lieu[]
     */
    public function getLieuDeSortie(): Collection
    {
        return $this->lieuDeSortie;
    }

    public function addLieuDeSortie(Lieu $lieuDeSortie): self
    {
        if (!$this->lieuDeSortie->contains($lieuDeSortie)) {
            $this->lieuDeSortie[] = $lieuDeSortie;
            $lieuDeSortie->setVille($this);
        }

        return $this;
    }

    public function removeLieuDeSortie(Lieu $lieuDeSortie): self
    {
        if ($this->lieuDeSortie->removeElement($lieuDeSortie)) {
            // set the owning side to null (unless already changed)
            if ($lieuDeSortie->getVille() === $this) {
                $lieuDeSortie->setVille(null);
            }
        }

        return $this;
    }
}
