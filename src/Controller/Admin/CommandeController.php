<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/commandes')]
class CommandeController extends AbstractController
{
    #[Route('', name: 'admin_commandes')]
    public function index(Request $request, CommandeRepository $repo): Response
    {
        $statut = $request->query->get('statut');
        $commandes = $repo->findByStatut($statut ?: null);

        return $this->render('admin/commandes/index.html.twig', [
            'commandes' => $commandes,
            'statut'    => $statut,
            'statuts'   => Commande::STATUTS,
        ]);
    }

    #[Route('/{id}', name: 'admin_commande_show', requirements: ['id' => '\d+'])]
    public function show(Commande $commande): Response
    {
        return $this->render('admin/commandes/show.html.twig', [
            'commande' => $commande,
        ]);
    }
}
