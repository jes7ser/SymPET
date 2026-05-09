# 🐾 SymPET - Application E-Commerce pour Animaux

**Projet Académique — L2 DSI**

SymPET est une application web e-commerce dédiée à la vente en ligne de nourriture et d'accessoires pour animaux de compagnie (chiens, chats, oiseaux, rongeurs, poissons). Ce projet repose sur Symfony 6/7.

## 👥 Auteurs
- **Étudiant 1** : Skander (Développement Front-Office, Panier, Peuplement API, UI/UX)
- **Étudiant 2** : Jesser (Développement Back-Office, Sécurité, Authentification)

---

## 🚀 Nouvelles Fonctionnalités Développées (Mise à jour)

### 🛒 Espace Client (Vitrine & Accueil)
- **Page d'Accueil Premium** : Design repensé (Hero section, animations au survol) intégrant des illustrations vectorielles (Twemoji) pour chaque catégorie d'animal.
- **Catalogue Dynamique** : Affichage d'une Boutique complète alimentée par de véritables données (noms, prix réels, images hébergées) extraites de l'API de *Zanimo.tn*.
- **Filtres Avancés** : 
  - Recherche par nom (texte libre)
  - Filtres par catégorie (Chien, Chat, Oiseau...)
  - Filtres par plage de prix avec sliders
  - **NOUVEAU** : Filtre par "Type de produit" (Nourriture ou Accessoire).
- **Pagination Optimisée** : Implémentation centralisée (`PaginationService`) utilisant `KnpPaginator`, avec une interface utilisateur de pagination sur mesure (boutons circulaires, compteurs "X sur Y produits").
- **Architecture Propre** : Séparation stricte MVC et refactoring des contrôleurs avec l'injection de services dédiés (`ProduitFilterService`).

### 🛍️ Gestion du Panier (Session)
- **Service dédié (`CartService`)** : Logique métier entièrement encapsulée interagissant avec la session Symfony.
- **Fonctionnalités complètes** : 
  - Ajout de produits depuis la vitrine et la page détail.
  - Consultation d'un tableau récapitulatif clair.
  - Modification des quantités (boutons `+` / `-`).
  - Suppression de produits.
  - Calcul dynamique du sous-total, de la livraison, et du montant global.
- **Indicateur Temps Réel** : Badge rouge dynamique dans la barre de navigation indiquant le nombre total d'articles présents dans le panier.

### 🗄️ Base de Données & Peuplement
- Entité `Produit` adaptée pour stocker les URL d'images externes (`imageUrl`) et le type de produit.
- **Script d'injection de données réelles (`populate_db.py`)** : Remplacement des anciennes "Fixtures" (données factices) par un script Python autonome qui se connecte à MySQL et injecte **50 produits réels premium** récupérés de *Zanimo.tn*.

---

## 💻 Pré-requis Techniques
- **PHP** 8.2 ou supérieur
- **Composer**
- Serveur local (Laragon, XAMPP, WAMP...)
- **Python 3** (avec `pymysql` et `python-dotenv`) pour injecter les vraies données.

---

## ⚙️ Installation Rapide (Pour le Jury / Enseignants)

Suivez ces étapes pour tester le projet localement :

**1. Cloner le dépôt et installer les dépendances**
```bash
git clone <VOTRE_LIEN_GITHUB_ICI>
cd SymPET
composer install
```

**2. Configuration de la base de données**
Copiez le fichier `.env` vers `.env.local` et modifiez la variable `DATABASE_URL` pour correspondre à vos identifiants MySQL locaux.
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

**3. Charger les VRAIES données (Catalogue Zanimo)**
*Attention, ce script vide les tables actuelles et insère un catalogue e-commerce cohérent avec des vraies photos.*
```bash
# Nécessite Python installé sur la machine
pip install pymysql python-dotenv
python populate_db.py
```

**4. Lancer le serveur local PHP**
```bash
php -S localhost:8000 -t public
```

🎯 **L'application est maintenant accessible sur : `http://localhost:8000`**

---
*Fin du document.*
