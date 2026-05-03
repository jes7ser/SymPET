<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // ── Création d'un Admin ─────────────────────────
        $admin = new User();
        $admin->setEmail('admin@sympet.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // ── Création d'utilisateurs ─────────────────────
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->hasher->hashPassword($user, 'user123'));
            $manager->persist($user);
        }

        // ── Création des Catégories ─────────────────────
        $categoriesNames = ['Chiens', 'Chats', 'Oiseaux', 'Rongeurs', 'Aquatiques'];
        $categories = [];

        foreach ($categoriesNames as $name) {
            $categorie = new Categorie();
            $categorie->setNom($name);
            $manager->persist($categorie);
            $categories[] = $categorie;
        }

        // ── Création des Produits ───────────────────────
        for ($i = 0; $i < 50; $i++) {
            $produit = new Produit();
            $produit->setNom($faker->words(3, true))
                ->setDescription($faker->paragraph())
                ->setPrix($faker->randomFloat(2, 5, 200))
                ->setStock($faker->numberBetween(0, 100))
                ->setImage('https://loremflickr.com/640/480/pet,animal?lock=' . $i) // Image placeholders
                ->setCategorie($faker->randomElement($categories));

            $manager->persist($produit);
        }

        $manager->flush();
    }
}
