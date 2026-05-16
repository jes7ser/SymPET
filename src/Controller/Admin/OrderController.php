<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/commande')]
#[IsGranted('ROLE_ADMIN')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'admin_order_index', methods: ['GET'])]
    public function index(Request $request, CommandeRepository $commandeRepository): Response
    {
        $status = $request->query->get('status');
        
        if ($status) {
            $orders = $commandeRepository->findBy(['statut' => $status], ['dateCreation' => 'DESC']);
        } else {
            $orders = $commandeRepository->findBy([], ['dateCreation' => 'DESC']);
        }

        return $this->render('admin/order/index.html.twig', [
            'commandes' => $orders,
            'current_status' => $status,
        ]);
    }

    #[Route('/{id}', name: 'admin_order_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('admin/order/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/change-status', name: 'admin_order_change_status', methods: ['POST'])]
    public function changeStatus(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $newStatus = $request->request->get('status');
        $validStatuses = ['En attente', 'En cours', 'Complétée', 'Annulée'];

        if (in_array($newStatus, $validStatuses)) {
            $oldStatus = $commande->getStatut();
            $commande->setStatut($newStatus);

            // Déduction du stock si la commande passe à "Complétée"
            if ($newStatus === 'Complétée' && $oldStatus !== 'Complétée') {
                foreach ($commande->getLigneCommandes() as $ligne) {
                    $produit = $ligne->getProduit();
                    if ($produit) {
                        $newStock = $produit->getStock() - $ligne->getQuantite();
                        $produit->setStock(max(0, $newStock)); // Empêche le stock négatif
                        
                        // Si le stock tombe à zéro, marquer en rupture
                        if ($produit->getStock() === 0) {
                            $produit->setIsRupture(true);
                        }
                    }
                }
            } 
            // Restauration du stock si la commande quitte le statut "Complétée"
            elseif ($oldStatus === 'Complétée' && $newStatus !== 'Complétée') {
                foreach ($commande->getLigneCommandes() as $ligne) {
                    $produit = $ligne->getProduit();
                    if ($produit) {
                        $produit->setStock($produit->getStock() + $ligne->getQuantite());
                        
                        if ($produit->getStock() > 0) {
                            $produit->setIsRupture(false);
                        }
                    }
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Le statut de la commande a été mis à jour.');
        }

        return $this->redirectToRoute('admin_order_show', ['id' => $commande->getId()]);
    }
}
