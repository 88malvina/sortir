<?php

namespace App\Modele;

use App\Entity\Campus;
use App\Entity\Participant;

class SortieSearch
{

//Déclarer tous les attributs
//on ajoute un param pour l'user

    /**
     * @var Participant
     */
    private $user;

    /**
     * @var string|null
     */
    private $nom;

    /**
     * @var Campus|null
     */
    private $campus;

    /**
     * @var \DateTime|null
     */
    private $dateHeureDebut;

    /**
     * @var \DateTime|null
     */
    private $dateLimiteInscription;

    /**
     * @var boolean|null
     */
    private $jeSuisOrganisateur;

    /**
     * @var boolean|null
     */
    private $jeSuisInscrit;

    /**
     * @var boolean|null
     */
    private $jeNeSuisPasInscrit;

    /**
     * @return Participant
     */
    public function getUser(): Participant
    {
        return $this->user;
    }

    /**
     * @param Participant $user
     */
    public function setUser(Participant $user): void
    {
        $this->user = $user;
    }



    /**
     * @return string|null
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * @param string|null $nom
     */
    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return Campus|null
     */
    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    /**
     * @param Campus|null $campus
     */
    public function setCampus(?Campus $campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateHeureDebut(): ?\DateTime
    {
        return $this->dateHeureDebut;
    }

    /**
     * @param \DateTime|null $dateHeureDebut
     */
    public function setDateHeureDebut(?\DateTime $dateHeureDebut): void
    {
        $this->dateHeureDebut = $dateHeureDebut;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateLimiteInscription(): ?\DateTime
    {
        return $this->dateLimiteInscription;
    }

    /**
     * @param \DateTime|null $dateLimiteInscription
     */
    public function setDateLimiteInscription(?\DateTime $dateLimiteInscription): void
    {
        $this->dateLimiteInscription = $dateLimiteInscription;
    }

    /**
     * @return bool|null
     */
    public function getJeSuisOrganisateur(): ?bool
    {
        return $this->jeSuisOrganisateur;
    }

    /**
     * @param bool|null $jeSuisOrganisateur
     */
    public function setJeSuisOrganisateur(?bool $jeSuisOrganisateur): void
    {
        $this->jeSuisOrganisateur = $jeSuisOrganisateur;
    }

    /**
     * @return bool|null
     */
    public function getJeSuisInscrit(): ?bool
    {
        return $this->jeSuisInscrit;
    }

    /**
     * @param bool|null $jeSuisInscrit
     */
    public function setJeSuisInscrit(?bool $jeSuisInscrit): void
    {
        $this->jeSuisInscrit = $jeSuisInscrit;
    }

    /**
     * @return bool|null
     */
    public function getJeNeSuisPasInscrit(): ?bool
    {
        return $this->jeNeSuisPasInscrit;
    }

    /**
     * @param bool|null $jeNeSuisPasInscrit
     */
    public function setJeNeSuisPasInscrit(?bool $jeNeSuisPasInscrit): void
    {
        $this->jeNeSuisPasInscrit = $jeNeSuisPasInscrit;
    }

    /**
     * @return bool|null
     */
    public function getSortiePassee(): ?bool
    {
        return $this->sortiePassee;
    }

    /**
     * @param bool|null $sortiePassee
     */
    public function setSortiePassee(?bool $sortiePassee): void
    {
        $this->sortiePassee = $sortiePassee;
    }

    /**
     * @var boolean|null
     */
    private $sortiePassee;

}