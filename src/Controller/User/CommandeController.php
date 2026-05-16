<?php

namespace App\Controller\User;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\CommandeRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user/commande')]
#[IsGranted('ROLE_USER')]
class CommandeController extends AbstractController
{
    /**
     * Affiche le formulaire de commande avec le récapitulatif du panier
     */
    #[Route('/new', name: 'app_user_commande_new', methods: ['GET'])]
    public function new(CartService $cartService): Response
    {
        $items = $cartService->getFullCart();

        // Rediriger vers le panier si celui-ci est vide
        if (empty($items)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart_index');
        }

        $user = $this->getUser();
        $total = $cartService->getTotal();

        return $this->render('user/commande/new.html.twig', [
            'items'  => $items,
            'total'  => $total,
            'user'   => $user,
        ]);
    }

    /**
     * Traite le formulaire de commande :
     * - Si paiement par carte → redirige vers Stripe Checkout
     * - Si paiement à la livraison → enregistre la commande et redirige vers succès
     */
    #[Route('/checkout', name: 'app_user_commande_checkout', methods: ['POST'])]
    public function checkout(
        Request $request,
        CartService $cartService,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $items = $cartService->getFullCart();

        if (empty($items)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart_index');
        }

        $user        = $this->getUser();
        $modePaiement = $request->request->get('modePaiement', 'livraison');
        $total        = $cartService->getTotal();

        // Récupération des données du formulaire
        $adresse     = $request->request->get('adresse', '');
        $telephone   = $request->request->get('telephone', '');
        $gouvernorat = $request->request->get('gouvernorat', '');
        $codePostal  = $request->request->get('codePostal', '');

        // ── Validation côté serveur ──────────────────────────────
        $errors = [];

        // Téléphone : exactement 8 chiffres
        if (!preg_match('/^[0-9]{8}$/', $telephone)) {
            $errors[] = 'Le numéro de téléphone doit contenir exactement 8 chiffres.';
        }

        // Code postal : exactement 4 chiffres
        if (!preg_match('/^[0-9]{4}$/', $codePostal)) {
            $errors[] = 'Le code postal doit contenir exactement 4 chiffres.';
        }

        // Adresse : non vide, min 5 caractères
        if (strlen(trim($adresse)) < 5) {
            $errors[] = 'L\'adresse de livraison est invalide (min 5 caractères).';
        }

        // Gouvernorat : doit être dans la liste
        $gouvernorats = ['Tunis','Ariana','Ben Arous','Manouba','Nabeul','Zaghouan',
            'Bizerte','Béja','Jendouba','Kef','Siliana','Sousse','Monastir',
            'Mahdia','Sfax','Kairouan','Kasserine','Sidi Bouzid','Gabès',
            'Médenine','Tataouine','Gafsa','Tozeur','Kébili'];
        if (!in_array($gouvernorat, $gouvernorats)) {
            $errors[] = 'Veuillez choisir un gouvernorat valide.';
        }

        // Mode de paiement : uniquement livraison ou stripe
        if (!in_array($modePaiement, ['livraison', 'stripe'])) {
            $errors[] = 'Mode de paiement invalide.';
        }

        // Si des erreurs → retour au formulaire avec messages
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->addFlash('danger', $error);
            }
            return $this->redirectToRoute('app_user_commande_new');
        }
        // ────────────────────────────────────────────────────────
        if ($modePaiement === 'stripe') {
            Stripe::setApiKey($this->getParameter('stripe_secret_key'));

            // Construire les line_items pour Stripe
            $lineItems = [];
            foreach ($items as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency'     => 'tnd',
                        'product_data' => ['name' => $item['produit']->getNom()],
                        'unit_amount'  => (int) round($item['produit']->getPrix() * 1000),
                    ],
                    'quantity' => $item['quantite'],
                ];
            }

            // Stocker les données de livraison en session pour les récupérer après le retour Stripe
            $request->getSession()->set('commande_data', [
                'adresse'     => $adresse,
                'telephone'   => $telephone,
                'gouvernorat' => $gouvernorat,
                'codePostal'  => $codePostal,
            ]);

            $stripeSession = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                'success_url'          => $this->generateUrl('app_user_commande_success', ['stripe' => 1], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url'           => $this->generateUrl('app_user_commande_new', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);

            return $this->redirect($stripeSession->url);
        }

        // Paiement à la livraison → enregistrement direct en base
        $commande = $this->enregistrerCommande($em, $user, $items, $total, $adresse, $telephone, $gouvernorat, $codePostal, 'livraison');

        // Vider le panier
        $cartService->clear();

        // Envoyer l'email de confirmation
        $this->envoyerEmailConfirmation($mailer, $user, $commande, $items);

        return $this->redirectToRoute('app_user_commande_success', ['id' => $commande->getId()]);
    }

    /**
     * Page de succès après paiement (Stripe ou livraison)
     */
    #[Route('/success', name: 'app_user_commande_success', methods: ['GET'])]
    public function success(
        Request $request,
        CartService $cartService,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $commandeId = $request->query->get('id');
        $isStripe   = $request->query->get('stripe');

        // Retour depuis Stripe : créer la commande maintenant
        if ($isStripe) {
            $items = $cartService->getFullCart();
            $user  = $this->getUser();
            $total = $cartService->getTotal();

            // Récupérer les données de livraison stockées en session
            $data = $request->getSession()->get('commande_data', []);

            $commande = $this->enregistrerCommande(
                $em, $user, $items, $total,
                $data['adresse'] ?? '',
                $data['telephone'] ?? '',
                $data['gouvernorat'] ?? '',
                $data['codePostal'] ?? '',
                'stripe'
            );

            $cartService->clear();
            $request->getSession()->remove('commande_data');
            $this->envoyerEmailConfirmation($mailer, $user, $commande, $items);

            $commandeId = $commande->getId();
        }

        // Charger la commande pour l'afficher
        $commande = $em->getRepository(Commande::class)->find($commandeId);

        if (!$commande || $commande->getUtilisateur() !== $this->getUser()) {
            throw $this->createNotFoundException('Commande introuvable.');
        }

        return $this->render('user/commande/success.html.twig', [
            'commande' => $commande,
        ]);
    }

    /**
     * Historique des commandes de l'utilisateur connecté
     */
    #[Route('/historique', name: 'app_user_commande_historique', methods: ['GET'])]
    public function historique(CommandeRepository $commandeRepository): Response
    {
        $commandes = $commandeRepository->findByUtilisateur($this->getUser());

        return $this->render('user/commande/historique.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    /**
     * Détail d'une commande — vérifie que la commande appartient à l'utilisateur connecté
     */
    #[Route('/show/{id}', name: 'app_user_commande_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): Response
    {
        $commande = $em->getRepository(Commande::class)->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande introuvable.');
        }

        // Sécurité : la commande doit appartenir à l'utilisateur connecté
        if ($commande->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès refusé à cette commande.');
        }

        return $this->render('user/commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    /**
     * Méthode privée : crée et persiste une commande en base de données
     */
    private function enregistrerCommande(
        EntityManagerInterface $em,
        $user,
        array $items,
        float $total,
        string $adresse,
        string $telephone,
        string $gouvernorat,
        string $codePostal,
        string $modePaiement
    ): Commande {
        $commande = new Commande();
        $commande->setUtilisateur($user);
        $commande->setDateCreation(new \DateTime());
        $commande->setStatut('En attente');
        $commande->setTotal($total);
        $commande->setAdresseLivraison($adresse);
        $commande->setTelephone($telephone);
        $commande->setGouvernorat($gouvernorat);
        $commande->setCodePostal($codePostal);
        $commande->setModePaiement($modePaiement);

        $em->persist($commande);

        // Créer les lignes de commande
        foreach ($items as $item) {
            $ligne = new LigneCommande();
            $ligne->setCommande($commande);
            $ligne->setProduit($item['produit']);
            $ligne->setQuantite($item['quantite']);
            $ligne->setPrixUnitaire($item['produit']->getPrix());
            $em->persist($ligne);
        }

        $em->flush();

        return $commande;
    }

    /**
     * Méthode privée : envoie un email de confirmation au client
     */
    private function envoyerEmailConfirmation(MailerInterface $mailer, $user, Commande $commande, array $items): void
    {
        try {
            $lignesHtml = '';
            foreach ($items as $item) {
                $lignesHtml .= sprintf(
                    '<tr><td>%s</td><td>%d</td><td>%.3f DT</td><td>%.3f DT</td></tr>',
                    $item['produit']->getNom(),
                    $item['quantite'],
                    $item['produit']->getPrix(),
                    $item['produit']->getPrix() * $item['quantite']
                );
            }

            $html = sprintf('
                <h2>Confirmation de votre commande #%d</h2>
                <p>Bonjour %s %s,</p>
                <p>Votre commande a bien été enregistrée.</p>
                <table border="1" cellpadding="8" style="border-collapse:collapse;width:100%%">
                    <thead><tr><th>Produit</th><th>Qté</th><th>Prix unitaire</th><th>Total</th></tr></thead>
                    <tbody>%s</tbody>
                </table>
                <p><strong>Total : %.3f DT</strong></p>
                <p><strong>Adresse de livraison :</strong> %s, %s %s</p>
                <p><strong>Mode de paiement :</strong> %s</p>
                <p>Merci pour votre confiance !</p>
            ',
                $commande->getId(),
                $user->getPrenom(),
                $user->getNom(),
                $lignesHtml,
                $commande->getTotal(),
                $commande->getAdresseLivraison(),
                $commande->getCodePostal(),
                $commande->getGouvernorat(),
                $commande->getModePaiement() === 'stripe' ? 'Paiement par carte' : 'Paiement à la livraison'
            );

            $email = (new Email())
                ->from('noreply@sympet.tn')
                ->to($user->getEmail())
                ->subject('Confirmation de commande #' . $commande->getId() . ' — SymPET')
                ->html($html);

            $mailer->send($email);
        } catch (\Exception $e) {
            // Ne pas bloquer si l'email échoue (MAILER_DSN=null en dev)
        }
    }
}
