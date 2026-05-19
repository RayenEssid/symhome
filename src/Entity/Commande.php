<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_EN_COURS   = 'en_cours';
    const STATUT_COMPLETEE  = 'completee';
    const STATUT_ANNULEE    = 'annulee';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $numero = null;

    #[ORM\Column(length: 30)]
    private string $statut = self::STATUT_EN_ATTENTE;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total = '0.00';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeSessionId = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $adresseLivraison = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: LigneCommande::class, cascade: ['persist', 'remove'])]
    private Collection $lignesCommande;

    public function __construct()
    {
        $this->lignesCommande = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->numero = 'CMD-' . strtoupper(uniqid());
    }

    public function getId(): ?int { return $this->id; }

    public function getNumero(): ?string { return $this->numero; }
    public function setNumero(string $numero): static { $this->numero = $numero; return $this; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }

    public function getTotal(): ?string { return $this->total; }
    public function setTotal(string $total): static { $this->total = $total; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getStripeSessionId(): ?string { return $this->stripeSessionId; }
    public function setStripeSessionId(?string $id): static { $this->stripeSessionId = $id; return $this; }

    public function getAdresseLivraison(): ?string { return $this->adresseLivraison; }
    public function setAdresseLivraison(?string $a): static { $this->adresseLivraison = $a; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getLignesCommande(): Collection { return $this->lignesCommande; }

    public function addLigneCommande(LigneCommande $ligne): static
    {
        if (!$this->lignesCommande->contains($ligne)) {
            $this->lignesCommande->add($ligne);
            $ligne->setCommande($this);
        }
        return $this;
    }

    public function calculerTotal(): void
    {
        $total = 0;
        foreach ($this->lignesCommande as $ligne) {
            $total += $ligne->getSousTotal();
        }
        $this->total = number_format($total, 2, '.', '');
    }

    public function getStatutLabel(): string
    {
        return match($this->statut) {
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_EN_COURS   => 'En cours',
            self::STATUT_COMPLETEE  => 'Complétée',
            self::STATUT_ANNULEE    => 'Annulée',
            default => $this->statut,
        };
    }

    public function getStatutBadgeClass(): string
    {
        return match($this->statut) {
            self::STATUT_EN_ATTENTE => 'warning',
            self::STATUT_EN_COURS   => 'info',
            self::STATUT_COMPLETEE  => 'success',
            self::STATUT_ANNULEE    => 'danger',
            default => 'secondary',
        };
    }
}
