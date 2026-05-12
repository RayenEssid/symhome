// src/Entity/Commande.php
<?php


use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_EN_COURS   = 'en_cours';
    public const STATUT_COMPLETEE  = 'completee';
    public const STATUT_ANNULEE    = 'annulee';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, unique: true)]
    private ?string $numero = null;

    #[ORM\Column(length: 20)]
    private string $statut = self::STATUT_EN_ATTENTE;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $total = 0;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: LigneCommande::class,
        cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $ligneCommandes;

    public function __construct()
    {
        $this->ligneCommandes = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->numero = 'CMD-' . strtoupper(uniqid());
    }

    public function getId(): ?int { return $this->id; }

    public function getNumero(): ?string { return $this->numero; }
    public function setNumero(string $numero): static { $this->numero = $numero; return $this; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }

    public function getTotal(): float { return $this->total; }
    public function setTotal(float $total): static { $this->total = $total; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getLigneCommandes(): Collection { return $this->ligneCommandes; }
    public function addLigneCommande(LigneCommande $ligne): static
    {
        if (!$this->ligneCommandes->contains($ligne)) {
            $this->ligneCommandes->add($ligne);
            $ligne->setCommande($this);
        }
        return $this;
    }
    public function removeLigneCommande(LigneCommande $ligne): static
    {
        if ($this->ligneCommandes->removeElement($ligne)) {
            if ($ligne->getCommande() === $this) { $ligne->setCommande(null); }
        }
        return $this;
    }

    public function calculerTotal(): void
    {
        $this->total = array_sum(
            $this->ligneCommandes->map(
                fn(LigneCommande $l) => $l->getPrixUnitaire() * $l->getQuantite()
            )->toArray()
        );
    }
}