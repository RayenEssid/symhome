<?php

namespace App\Entity;

use App\Repository\MeubleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeubleRepository::class)]
#[ORM\Table(name: '`meuble`')]
#[ORM\HasLifecycleCallbacks]
class Meuble
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $nom = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?float $prix = null;

    #[ORM\Column(type: 'integer')]
    private int $stock = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'meubles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\OneToMany(mappedBy: 'meuble', targetEntity: LigneCommande::class)]
    private Collection $ligneCommandes;

    public function __construct()
    {
        $this->ligneCommandes = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int 
    { 
        return $this->id; 
    }

    public function getNom(): ?string 
    { 
        return $this->nom; 
    }
    
    public function setNom(string $nom): static 
    { 
        $this->nom = $nom; 
        return $this; 
    }

    public function getDescription(): ?string 
    { 
        return $this->description; 
    }
    
    public function setDescription(string $description): static 
    { 
        $this->description = $description; 
        return $this; 
    }

    public function getPrix(): ?float 
    { 
        return $this->prix; 
    }
    
    public function setPrix(float $prix): static 
    { 
        $this->prix = $prix; 
        return $this; 
    }

    public function getStock(): int 
    { 
        return $this->stock; 
    }
    
    public function setStock(int $stock): static 
    { 
        $this->stock = $stock; 
        return $this; 
    }

    public function getImage(): ?string 
    { 
        return $this->image; 
    }
    
    public function setImage(?string $image): static 
    { 
        $this->image = $image; 
        return $this; 
    }

    public function getCreatedAt(): ?\DateTimeImmutable 
    { 
        return $this->createdAt; 
    }
    
    public function setCreatedAt(\DateTimeImmutable $createdAt): static 
    { 
        $this->createdAt = $createdAt; 
        return $this; 
    }

    public function getCategorie(): ?Categorie 
    { 
        return $this->categorie; 
    }
    
    public function setCategorie(?Categorie $categorie): static 
    { 
        $this->categorie = $categorie; 
        return $this; 
    }

    public function getLigneCommandes(): Collection 
    { 
        return $this->ligneCommandes; 
    }

    public function __toString(): string 
    { 
        return $this->nom ?? ''; 
    }
}