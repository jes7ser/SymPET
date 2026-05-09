<?php

namespace App\Service;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    public function __construct(
        private RequestStack $requestStack,
        private ProduitRepository $produitRepository
    ) {
    }

    public function add(int $id): void
    {
        $session = $this->requestStack->getSession();
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $session->set('panier', $panier);
    }

    public function remove(int $id): void
    {
        $session = $this->requestStack->getSession();
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);
    }

    public function decrement(int $id): void
    {
        $session = $this->requestStack->getSession();
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id]--;
            } else {
                unset($panier[$id]);
            }
        }

        $session->set('panier', $panier);
    }

    public function getFullCart(): array
    {
        $session = $this->requestStack->getSession();
        $panier = $session->get('panier', []);

        $panierData = [];

        foreach ($panier as $id => $quantity) {
            $produit = $this->produitRepository->find($id);
            if (!$produit) {
                $this->remove($id);
                continue;
            }

            $panierData[] = [
                'produit' => $produit,
                'quantite' => $quantity
            ];
        }

        return $panierData;
    }

    public function getTotal(): float
    {
        $total = 0;

        foreach ($this->getFullCart() as $item) {
            $total += $item['produit']->getPrix() * $item['quantite'];
        }

        return $total;
    }

    public function getTotalQuantity(): int
    {
        $session = $this->requestStack->getSession();
        $panier = $session->get('panier', []);
        
        $total = 0;
        foreach ($panier as $quantity) {
            $total += $quantity;
        }
        
        return $total;
    }
}
