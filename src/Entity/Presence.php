<?php

namespace App\Entity;

use App\Repository\PresenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PresenceRepository::class)]
class Presence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $anne;

    #[ORM\Column(type: 'string', length: 255)]
    private $mois;

    #[ORM\Column(type: 'string', length: 255)]
    private $date;

    #[ORM\Column(type: 'string', length: 255,nullable:false)]
    private $heureEntre;

    #[ORM\Column(type: 'string', length: 255,nullable:true)]
    private $heureSortie;

    #[ORM\ManyToOne(targetEntity: Employe::class, inversedBy: 'presences',cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private $employe;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'presences',cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private $user;

    public function tojson(): ?array
    {
        return $this ? [
            'id' => $this->id,
            'anne' => $this->anne,
            'mois' => $this->mois,
            'date' => $this->date,
            'heureEntre' => $this->heureEntre,
            'heureSortie'=>$this->heureSortie,
            'employe'=>$this->employe?$this->employe->tojson():null,
            'user'=>$this->user?$this->user->tojson():null,
        ] : null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnne(): ?string
    {
        return $this->anne;
    }

    public function setAnne(string $anne): self
    {
        $this->anne = $anne;

        return $this;
    }

    public function getMois(): ?string
    {
        return $this->mois;
    }

    public function setMois(string $mois): self
    {
        $this->mois = $mois;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getHeureEntre(): ?string
    {
        return $this->heureEntre;
    }

    public function setHeureEntre(string $heureEntre): self
    {
        $this->heureEntre = $heureEntre;

        return $this;
    }

    public function getHeureSortie(): ?string
    {
        return $this->heureSortie;
    }

    public function setHeureSortie(string $heureSortie): self
    {
        $this->heureSortie = $heureSortie;

        return $this;
    }

    public function getEmploye(): ?Employe
    {
        return $this->employe;
    }

    public function setEmploye(?Employe $employe): self
    {
        $this->employe = $employe;

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
}
