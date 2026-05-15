<?php

namespace App\Entity;

use App\Repository\LigneCommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneCommandeRepository::class)]
#[ORM\Table(name: '`ligne_commande`')]
class LigneCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $quantite = 1;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $prixUnitaire = 0.0;

    #[ORM\ManyToOne(inversedBy: 'ligneCommandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(inversedBy: 'ligneCommandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Meuble $meuble = null;

    public function getId(): ?int 
    { 
        return $this->id; 
    }

    public function getQuantite(): int 
    { 
        return $this->quantite; 
    }
    
    public function setQuantite(int $quantite): static 
    { 
        $this->quantite = $quantite; 
        return $this; 
    }

    public function getPrixUnitaire(): float 
    { 
        return $this->prixUnitaire; 
    }
    
    public function setPrixUnitaire(float $prixUnitaire): static 
    { 
        $this->prixUnitaire = $prixUnitaire; 
        return $this; 
    }

    public function getCommande(): ?Commande 
    { 
        return $this->commande; 
    }
    
    public function setCommande(?Commande $commande): static 
    { 
        $this->commande = $commande; 
        return $this; 
    }

    public function getMeuble(): ?Meuble 
    { 
        return $this->meuble; 
    }
    
    public function setMeuble(?Meuble $meuble): static 
    { 
        $this->meuble = $meuble; 
        return $this; 
    }

    public function getSousTotal(): float
    {
        return $this->prixUnitaire * $this->quantite;
    }
}