<?php

namespace App\Service;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class PanierService
{
    public function __construct(
        private RequestStack $requestStack,
        private ProduitRepository $produitRepository
    ) {}

    private function getSession()
    {
        return $this->requestStack->getSession();
    }

    // Retourne le panier brut : [id_produit => quantite]
    public function getPanier(): array
    {
        return $this->getSession()->get('panier', []);
    }

    // Ajouter ou incrémenter un produit
    public function ajouter(int $id): void
    {
        $panier = $this->getPanier();
        $panier[$id] = ($panier[$id] ?? 0) + 1;
        $this->getSession()->set('panier', $panier);
    }

    // Modifier la quantité directement
    public function modifier(int $id, int $quantite): void
    {
        $panier = $this->getPanier();
        if ($quantite <= 0) {
            unset($panier[$id]);
        } else {
            $panier[$id] = $quantite;
        }
        $this->getSession()->set('panier', $panier);
    }

    // Supprimer un produit
    public function supprimer(int $id): void
    {
        $panier = $this->getPanier();
        unset($panier[$id]);
        $this->getSession()->set('panier', $panier);
    }

    // Vider le panier
    public function vider(): void
    {
        $this->getSession()->remove('panier');
    }

    // Retourne le panier enrichi avec les objets Produit
    public function getPanierComplet(): array
    {
        $panier = $this->getPanier();
        $panierComplet = [];

        foreach ($panier as $id => $quantite) {
            $produit = $this->produitRepository->find($id);
            if ($produit) {
                $panierComplet[] = [
                    'produit'   => $produit,
                    'quantite'  => $quantite,
                    'sousTotal' => $produit->getPrix() * $quantite,
                ];
            }
        }

        return $panierComplet;
    }

    // Calcule le total du panier
    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getPanierComplet() as $item) {
            $total += $item['sousTotal'];
        }
        return $total;
    }

    // Nombre total d'articles
    public function getNbArticles(): int
    {
        return array_sum($this->getPanier());
    }
}
