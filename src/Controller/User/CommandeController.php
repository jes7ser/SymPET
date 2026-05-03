<?php

namespace App\Controller\User;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use App\Service\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/valider', name: 'commande_valider')]
    public function valider(
        Request $request,
        PanierService $panierService,
        UserRepository $userRepo,
        EntityManagerInterface $em
    ): Response {
        $items = $panierService->getPanierComplet();

        if (empty($items)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('panier_index');
        }

        // Afficher le formulaire de sélection utilisateur (GET)
        if ($request->isMethod('GET')) {
            return $this->render('user/commande/valider.html.twig', [
                'items'  => $items,
                'total'  => $panierService->getTotal(),
                'users'  => $userRepo->findAll(),
            ]);
        }

        // Traitement POST
        $userId = $request->request->get('utilisateur_id');
        $utilisateur = $userId ? $userRepo->find($userId) : null;

        // Créer la commande
        $commande = new Commande();
        $commande->setDateCreation(new \DateTime());
        $commande->setStatut('pending');
        $commande->setUtilisateur($utilisateur);

        // Créer les lignes de commande
        foreach ($items as $item) {
            $ligne = new LigneCommande();
            $ligne->setProduit($item['produit']);
            $ligne->setQuantite($item['quantite']);
            $ligne->setPrixUnitaire($item['produit']->getPrix());
            $commande->addLigne($ligne);
        }

        $em->persist($commande);
        $em->flush();

        $panierService->vider();

        $this->addFlash('success', 'Commande passée avec succès !');
        return $this->redirectToRoute('commande_confirmation', ['id' => $commande->getId()]);
    }

    #[Route('/confirmation/{id}', name: 'commande_confirmation', requirements: ['id' => '\d+'])]
    public function confirmation(Commande $commande): Response
    {
        return $this->render('user/commande/confirmation.html.twig', [
            'commande' => $commande,
        ]);
    }
}
