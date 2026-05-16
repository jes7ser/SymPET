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

            $deductedStatuses = ['En attente', 'En cours', 'Complétée'];
            
            $wasDeducted = in_array($oldStatus, $deductedStatuses);
            $isDeducted = in_array($newStatus, $deductedStatuses);

            // Si on passe d'un statut non déduit à un statut déduit -> on déduit le stock
            if (!$wasDeducted && $isDeducted) {
                foreach ($commande->getLigneCommandes() as $ligne) {
                    $produit = $ligne->getProduit();
                    if ($produit) {
                        $newStock = $produit->getStock() - $ligne->getQuantite();
                        $produit->setStock(max(0, $newStock));
                        if ($produit->getStock() === 0) {
                            $produit->setIsRupture(true);
                        }
                    }
                }
            } 
            // Si on passe d'un statut déduit à un statut non déduit (ex: Annulée) -> on restaure le stock
            elseif ($wasDeducted && !$isDeducted) {
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

    #[Route('/{id}/delete', name: 'admin_order_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $request->request->get('_token'))) {
            // Restore stock if the order was in a deducted status
            $deductedStatuses = ['En attente', 'En cours', 'Complétée'];
            if (in_array($commande->getStatut(), $deductedStatuses)) {
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

            $entityManager->remove($commande);
            $entityManager->flush();
            $this->addFlash('success', 'La commande a été supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_order_index');
    }
}
