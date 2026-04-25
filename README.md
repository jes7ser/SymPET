# 🐾 SymPET - Application Web E-Commerce

**Projet Académique — L2 DSI**

SymPET est une application web e-commerce dédiée à la vente en ligne de nourriture et d'accessoires pour animaux de compagnie (chiens, chats, oiseaux, rongeurs, poissons). 

## 👨‍💻 Auteurs
- **Étudiant 1** : Skander
- **Étudiant 2** : jesser

---

## 🚀 Fonctionnalités Actuelles (Étape 3)

### Espace Administration (CRUD)
- ✅ Gestion complète des **Catégories** (Ajout, Édition, Suppression, Liste formattée).
- ✅ Gestion complète des **Produits** (Ajout, Édition, Suppression).
- ✅ **Upload d'images** sécurisé avec `SluggerInterface` pour les fiches produits.
- ✅ Pagination dynamique intégrée avec `KnpPaginatorBundle`.
- ✅ Design responsive via **Bootstrap 5** natif.
- ✅ Notifications Flash (Succès / Erreur) sur chaque action.

### Base de Données & Fixtures
- ✅ Génération massive de données de tests (`FakerPHP`).
- ✅ Génération automatique : 5 Catégories, 50 Produits avec images et prix.
- ✅ Base de données relationnelle saine sous **Doctrine ORM**.

*(À venir : Vitrine Client, Panier, Commande et Tableau de bord Admin).*

---

## 🛠️ Pré-requis Techniques
Pour faire tourner ce projet sur votre machine locale, vous aurez besoin de :
- **PHP** 8.2 ou supérieur
- **Composer**
- Un serveur de base de données (MySQL )

---

## 💻 Installation Rapide (Pour le Jury / Enseignants)

Suivez ces étapes pour cloner et lancer le projet sur votre machine.

**1. Cloner le dépôt et installer les dépendances**
```bash
git clone <VOTRE_LIEN_GITHUB_ICI>
cd SymPET
composer install
```

**2. Configuration de la base de données**
*(Modifiez le fichier `.env.local` si votre mot de passe MySQL n'est pas vide)*
```bash
# S'assurer que le fichier .env.local a la bonne DATABASE_URL :
# DATABASE_URL="mysql://root:@127.0.0.1:3306/sympet?serverVersion=10.11.2-MariaDB&charset=utf8mb4"

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

**3. Charger les fausses données de test (Fixtures)**
*Attention, cela réinitialise les tables existantes.*
```bash
php bin/console doctrine:fixtures:load --no-interaction
```

**4. Lancer le serveur local PHP**
```bash
php -S localhost:8000 -t public
```

👉 L'application est maintenant accessible sur : `http://localhost:8000`

---


