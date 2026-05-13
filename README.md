# 🐾 SymPET - Application E-Commerce pour Animaux

**Projet Académique — L2 DSI**

SymPET est une application web e-commerce dédiée à la vente en ligne de nourriture et d'accessoires pour animaux de compagnie (chiens, chats, oiseaux, rongeurs, poissons). Ce projet repose sur Symfony 6/7.

---

## 👥 Auteurs

| Rôle | Étudiant | Responsabilités |
|---|---|---|
| **Front-Office / User** | **Skander** | Vitrine, Panier, Commande, Paiement Stripe, Historique, UI/UX |
| **Back-Office / Admin** | **Jesser** | Dashboard, CRUD Produits/Catégories/Commandes, Sécurité, Auth |

---

## 🚀 Fonctionnalités

### � Authentification & Sécurité
- Inscription avec confirmation par email (`isEnabled`)
- Connexion avec redirection selon le rôle : **Admin → Dashboard**, **Client → Accueil**
- Flash message de bienvenue après connexion
- `UserChecker` : bloque les comptes non activés
- Protection des routes via `access_control` dans `security.yaml`

### 🛒 Espace Client (Vitrine)
- **Catalogue dynamique** : 50 produits réels avec images de *Zanimo.tn*
- **Filtres avancés** : nom, catégorie, plage de prix (sliders), type (Nourriture/Accessoire), promos
- **Pagination** : `KnpPaginator` via `PaginationService` — affichage "X–Y sur Z produits"
- **Page détail produit** : image, description, prix, badge promo, stock
- **Badges** : promotion (`-X%`), rupture de stock

### 🛍️ Panier (Session)
- Ajout / suppression / modification de quantité
- Bouton `-` masqué quand quantité = 1 (apparaît dès qu'on clique `+`)
- Badge rouge en temps réel dans la navbar
- Bouton **"Valider et commander"** (vert si connecté, jaune sinon → redirige vers login)

### � Commande & Paiement *(Skander)*
- **Formulaire de commande** en 2 colonnes :
  - Adresse de livraison (nom, prénom, email pré-remplis, téléphone, rue, gouvernorat, code postal)
  - 24 gouvernorats tunisiens en select
  - Mode de paiement : **Paiement à la livraison** ou **Paiement par carte (Stripe)**
  - Champs carte Stripe affichés dynamiquement
- **Validation complète** :
  - Côté client : HTML5 (`pattern`, `minlength`, `maxlength`) + Bootstrap `was-validated`
  - Côté serveur : `preg_match` PHP (téléphone 8 chiffres, code postal 4 chiffres, adresse min 5 chars, gouvernorat dans liste)
  - Saisie auto-filtrée : téléphone et code postal acceptent uniquement des chiffres
- **Intégration Stripe Checkout** : redirection vers page de paiement sécurisée Stripe
- **Paiement à la livraison** : enregistrement direct en base avec statut `en_attente`
- **Email de confirmation** automatique via Symfony Mailer (HTML avec tableau des articles)
- **Page de succès** : numéro de commande, récapitulatif articles, adresse, mode de paiement

### � Historique des Commandes *(Skander)*
- Liste de toutes les commandes de l'utilisateur connecté (triées par date décroissante)
- Tableau Bootstrap avec badges colorés : 🟢 Livrée / 🟠 En attente / 🔴 Annulée
- Bouton **"Voir détail"** par commande
- **Page détail** : articles, quantités, prix unitaires, adresse de livraison, mode de paiement
- Sécurité : vérification que la commande appartient à l'utilisateur (403 sinon)
- Accessible depuis le menu profil (dropdown navbar)

### 🏠 Back-Office Admin *(Jesser)*
- Dashboard avec statistiques : total clients, revenus, meilleur produit, graphique mensuel
- CRUD complet : Produits, Catégories, Commandes, Utilisateurs, Avis
- Gestion des images produits (upload local ou URL externe)
- Champs promo : `isPromo`, `promotion` (%), `isRupture`

---

## 🗄️ Modèle de Données

```
User ──< Commande ──< LigneCommande >── Produit >── Categorie
User ──< Avis >── Produit
Commande : id, dateCreation, statut, total, adresseLivraison,
           telephone, gouvernorat, codePostal, modePaiement
```

---

## 💻 Pré-requis

- PHP 8.2+
- Composer
- MySQL 8+
- Serveur local (Laragon, XAMPP...)
- Python 3 + `pymysql` + `python-dotenv` (pour peupler la BDD)
- Compte Stripe (pour tester le paiement par carte)

---

## ⚙️ Installation

**1. Cloner et installer**
```bash
git clone https://github.com/jes7ser/SymPET.git
cd SymPET
composer install
```

**2. Configurer la base de données**

Copier `.env` vers `.env.local` et modifier `DATABASE_URL` :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

**3. Peupler avec les vraies données**
```bash
pip install pymysql python-dotenv
python populate_db.py
```

**4. Créer un compte admin**
```bash
php create_admin.php
```

**5. Configurer Stripe** (optionnel, pour tester le paiement carte)

Dans `.env.local` :
```
STRIPE_SECRET_KEY=sk_test_votre_cle
STRIPE_PUBLIC_KEY=pk_test_votre_cle
```

**6. Lancer le serveur**
```bash
symfony server:start
# ou
php -S localhost:8000 -t public
```

🎯 **Accessible sur : `http://localhost:8000`**

---

## 🧪 Comptes de test

| Rôle | Email | Mot de passe |
|---|---|---|
| Admin | admin@sympet.tn | *(défini via create_admin.php)* |
| Client | khalil@gmail.com | *(votre mot de passe)* |

---

## 📁 Structure des fichiers clés

```
src/
├── Controller/
│   ├── Admin/          ← CRUD back-office (Jesser)
│   └── User/
│       ├── VitrineController.php    ← Catalogue + filtres
│       ├── CartController.php       ← Panier session
│       └── CommandeController.php   ← Commande + Stripe + Historique (Skander)
├── Entity/             ← User, Produit, Categorie, Commande, LigneCommande, Avis
├── Repository/         ← Requêtes Doctrine
├── Security/           ← LoginSuccessHandler, UserChecker
└── Service/
    ├── CartService.php          ← Logique panier session
    ├── ProduitFilterService.php ← Filtrage + pagination
    └── PaginationService.php    ← KnpPaginator centralisé
templates/
├── admin/              ← Vues back-office
└── user/
    ├── vitrine/        ← Catalogue, détail produit
    ├── cart/           ← Panier
    └── commande/       ← Formulaire, succès, historique, détail (Skander)
```

---

*Projet L2 DSI — SymPET © 2026*
