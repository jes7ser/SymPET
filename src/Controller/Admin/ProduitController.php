<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'admin_produit_index')]
    public function index(ProduitRepository $produitRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $produitRepository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/produit/index.html.twig', [
            'produits' => $pagination,
        ]);
    }

    #[Route('/new', name: 'admin_produit_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $produit = $form->getData();
                $entityManager->persist($produit);
                $entityManager->flush();

                $this->addFlash('success', 'Produit ajouté avec succès !');
                return $this->redirectToRoute('admin_produit_index');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Erreur : ' . $e->getMessage());
            }
        }

        return $this->render('admin/produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_produit_edit')]
    public function edit(Request $request, ProduitRepository $rep, $id, EntityManagerInterface $entityManager): Response
    {
        $produit = $rep->find($id);
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $produit = $form->getData();
                $entityManager->flush();

                $this->addFlash('success', 'Produit modifié avec succès !');
                return $this->redirectToRoute('admin_produit_index');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Erreur : ' . $e->getMessage());
            }
        }

        return $this->render('admin/produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_produit_delete')]
    public function delete(ProduitRepository $rep, $id, EntityManagerInterface $entityManager): Response
    {
        $produit = $rep->find($id);
        $entityManager->remove($produit);
        $entityManager->flush();

        $this->addFlash('success', 'Produit supprimé !');
        return $this->redirectToRoute('admin_produit_index');
    }
}
