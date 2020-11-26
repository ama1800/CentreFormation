<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use App\Repository\SessionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SessionRepository::class)
 */
class Session
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("session_prop")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("session_prop")
     */
    private $libelle;

    /**
     * @ORM\Column(type="date")
     * @Groups("session_prop")
     */
    private $startAt;

    /**
     * @ORM\Column(type="date")
     * @Groups("session_prop")
     */
    private $endAt;

    /**
     * @ORM\ManyToOne(targetEntity=Formation::class, inversedBy="sessions",cascade="all")
     */
    private $formation;

    /**
     * @ORM\ManyToMany(targetEntity=Stagiaire::class, mappedBy="sessions" ,cascade="all")     
     * @Assert\Valid()
     */
    private $stagiaires;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("session_prop")
     */
    private $photo;

    

    public function __construct()
    {
        $this->stagiaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation=null): self
    {
        $this->formation = $formation;

        return $this;
    }

    /**
     * @return Collection|Stagiaire[]
     */
    public function getStagiaires(): Collection
    {
        return $this->stagiaires;
    }

    public function addStagiaire(Stagiaire $stagiaire): self
    {
        if (!$this->stagiaires->contains($stagiaire)) {
            $this->stagiaires[] = $stagiaire;
            $stagiaire->addSession($this);
        }

        return $this;
    }

    public function removeStagiaire(Stagiaire $stagiaire): self
    {
        if ($this->stagiaires->removeElement($stagiaire)) {
            $stagiaire->removeSession($this);
        }

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }
}
