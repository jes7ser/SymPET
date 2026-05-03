<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/categorie')]
class CategorieController extends AbstractController
{
    #[Route('/', name: 'admin_categorie_index')]
    public function index(CategorieRepository $categorieRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $categorieRepository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/categorie/index.html.twig', [
            'categories' => $pagination,
        ]);
    }

    #[Route('/new', name: 'admin_categorie_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $categorie = $form->getData();
                $entityManager->persist($categorie);
                $entityManager->flush();

                $this->addFlash('success', 'Catégorie ajoutée avec succès !');
                return $this->redirectToRoute('admin_categorie_index');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Une erreur est survenue lors de l\'ajout');
            }
        }

        return $this->render('admin/categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_categorie_edit')]
    public function edit(Request $request, CategorieRepository $rep, $id, EntityManagerInterface $entityManager): Response
    {
        $categorie = $rep->find($id);
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $categorie = $form->getData();
                $entityManager->flush();

                $this->addFlash('success', 'Catégorie modifiée avec succès !');
                return $this->redirectToRoute('admin_categorie_index');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Une erreur est survenue lors de la modification');
            }
        }

        return $this->render('admin/categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_categorie_delete')]
    public function delete(CategorieRepository $rep, $id, EntityManagerInterface $entityManager): Response
    {
        $categorie = $rep->find($id);
        $entityManager->remove($categorie);
        $entityManager->flush();

        $this->addFlash('success', 'Catégorie supprimée !');
        return $this->redirectToRoute('admin_categorie_index');
    }
}
