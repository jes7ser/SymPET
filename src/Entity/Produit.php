<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, $payload): void
    {
        if ($this->isPromo && $this->isRupture) {
            $context->buildViolation('Un produit ne peut pas être en promotion et en rupture de stock en même temps.')
                ->atPath('isPromo')
                ->addViolation();
        }

        if ($this->isPromo && ($this->promotion === null || $this->promotion <= 0)) {
            $context->buildViolation('Veuillez saisir un pourcentage de remise valide pour la promotion.')
                ->atPath('promotion')
                ->addViolation();
        }
    }
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom du produit est obligatoire')]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le prix est obligatoire')]
    #[Assert\Positive(message: 'Le prix doit être supérieur à 0')]
    private ?float $prix = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le stock est obligatoire')]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'Le stock ne peut pas être négatif')]
    private ?int $stock = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: LigneCommande::class)]
    private Collection $ligneCommandes;

    #[ORM\Column]
    private bool $isPromo = false;

    #[ORM\Column(nullable: true)]
    private ?int $promotion = null;

    #[ORM\Column]
    private bool $isRupture = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $animalType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $produitType = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

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

    public function getStock(): ?int
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

    public function setImage(string $image): static
    {
        $this->image = $image;

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

    /**
     * @return Collection<int, LigneCommande>
     */
    public function getLigneCommandes(): Collection
    {
        return $this->ligneCommandes;
    }

    public function addLigneCommande(LigneCommande $ligneCommande): static
    {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes->add($ligneCommande);
            $ligneCommande->setProduit($this);
        }

        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): static
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            // set the owning side to null (unless already changed)
            if ($ligneCommande->getProduit() === $this) {
                $ligneCommande->setProduit(null);
            }
        }

        return $this;
    }
    public function isPromo(): bool
    {
        return $this->isPromo;
    }

    public function setIsPromo(bool $isPromo): static
    {
        $this->isPromo = $isPromo;
        return $this;
    }

    public function getPromotion(): ?int
    {
        return $this->promotion;
    }

    public function setPromotion(?int $promotion): static
    {
        $this->promotion = $promotion;
        return $this;
    }

    public function isRupture(): bool
    {
        return $this->isRupture;
    }

    public function setIsRupture(bool $isRupture): static
    {
        $this->isRupture = $isRupture;
        return $this;
    }

    public function getPrixFinal(): float
    {
        if ($this->isPromo && $this->promotion > 0) {
            return $this->prix * (1 - $this->promotion / 100);
        }
        return $this->prix;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getAnimalType(): ?string
    {
        return $this->animalType;
    }

    public function setAnimalType(?string $animalType): static
    {
        $this->animalType = $animalType;
        return $this;
    }

    public function getProduitType(): ?string
    {
        return $this->produitType;
    }

    public function setProduitType(?string $produitType): static
    {
        $this->produitType = $produitType;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
