<?php

namespace App\Controller\Admin;

use App\Repository\CommandeRepository;
use App\Repository\LigneCommandeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_dashboard')]
    public function index(
        UserRepository $userRepository,
        CommandeRepository $commandeRepository,
        LigneCommandeRepository $ligneCommandeRepository
    ): Response {
        $totalClients = $userRepository->countClients();
        $totalRevenue = $commandeRepository->getTotalRevenue();
        $bestSeller = $ligneCommandeRepository->getBestSeller();
        $monthlyOrders = $commandeRepository->getMonthlyOrders();

        // Préparation des données pour Chart.js
        $chartLabels = [];
        $chartData = [];
        $totalOrders = 0;
        foreach ($monthlyOrders as $order) {
            $chartLabels[] = $order['month'];
            $chartData[] = $order['count'];
            $totalOrders += $order['count'];
        }

        return $this->render('admin/dashboard/index.html.twig', [
            'totalClients' => $totalClients,
            'totalRevenue' => $totalRevenue,
            'bestSeller' => $bestSeller,
            'totalOrders' => $totalOrders,
            'chartLabels' => json_encode($chartLabels),
            'chartData' => json_encode($chartData),
        ]);
    }
}
