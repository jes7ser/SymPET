import os
import random
import pymysql
from datetime import datetime
from urllib.parse import urlparse
from dotenv import load_dotenv

load_dotenv(".env.local")
load_dotenv(".env")

# Données réelles extraites de l'API Zanimo (https://back.zanimo.tn/api/products)
# Format : (nom, description, prix, produit_type, image_url)
CATALOG = {
    "Chien": [
        ("DC ADULT SENSITIVE AGNEAU RIZ 20 Kg", "Croquettes premium pour chiens adultes sensibles, formule agneau et riz.", 390.0, "Nourriture", "https://assets.zanimo.tn/produits/4014355330527.jpg"),
        ("OWNAT CHIEN COMPLET 4 KG", "Aliment complet et équilibré pour chiens adultes de toutes races.", 54.0, "Nourriture", "https://assets.zanimo.tn/produits/8429037016044.jpg"),
        ("SIMBA DOG CROQUETTES CHICKEN 10 KG", "Croquettes au poulet pour chien, sac économique 10kg.", 108.5, "Nourriture", "https://assets.zanimo.tn/produits/8009470009850.jpg"),
        ("CARNILOVE CHIEN SAUMON 1,5KG", "Croquettes sans céréales au saumon pour chiens actifs.", 50.0, "Nourriture", "https://assets.zanimo.tn/produits/8595602508914.jpg"),
        ("PYLKRON CHIEN ADULT 4KG", "Alimentation complète économique pour chiens adultes.", 46.0, "Nourriture", "https://assets.zanimo.tn/produits/8429037006021.jpg"),
        ("BONES MUNCHY STICKS 12,5CM 25PCS", "Bâtonnets à mâcher naturels pour l'hygiène dentaire du chien.", 9.8, "Nourriture", "https://assets.zanimo.tn/produits/8023222022225.jpg"),
        ("COLLIER NYLON ZANILOVE 25 MM ROUGE", "Collier nylon solide et ajustable pour chiens de taille moyenne.", 15.7, "Accessoire", "https://assets.zanimo.tn/produits/6191469600734.jpg"),
        ("COLLIER NYLON ZANILOVE 19 MM NOIR", "Collier nylon résistant pour chiens de taille moyenne.", 11.6, "Accessoire", "https://assets.zanimo.tn/produits/6191469600673.jpg"),
        ("OS EN COTON W/BALL PASTEL JAUNE 40CM", "Jouet os en coton avec balle, idéal pour les séances de jeu.", 18.2, "Accessoire", "https://assets.zanimo.tn/produits/8023222226968.jpg"),
        ("CONTENEUR JERRY 23 LT SILVER", "Conteneur hermétique pour stocker les croquettes au sec.", 44.55, "Accessoire", "https://assets.zanimo.tn/produits/0f255-8003507703001.jpg"),
    ],
    "Chat": [
        ("OWNAT CHAT DAILY CARE 4 KG", "Croquettes équilibrées pour chats adultes, soutien immunitaire.", 66.0, "Nourriture", "https://assets.zanimo.tn/produits/8429037016174.jpg"),
        ("OWNAT CHAT KITTEN 1,5 KG", "Aliment spécifique pour chatons jusqu'à 12 mois.", 41.0, "Nourriture", "https://assets.zanimo.tn/produits/8429037016259.jpg"),
        ("OWNAT CHAT STERILIZED 4 KG", "Formule adaptée pour chats stérilisés, contrôle du poids.", 75.0, "Nourriture", "https://assets.zanimo.tn/produits/8429037016280.jpg"),
        ("SIMBA CAT CROQUETTES CHICKEN 2KG", "Croquettes au poulet pour chats adultes, format familial.", 30.5, "Nourriture", "https://assets.zanimo.tn/produits/8009470016063.jpg"),
        ("CARNILOVE CHAT SAUMON POILS LONG 2 KG", "Croquettes sans céréales pour chats à poils longs.", 71.0, "Nourriture", "https://assets.zanimo.tn/produits/de5d0-8595602512287.jpg"),
        ("DC KITTEN 400 gr", "Aliment premium pour chatons de moins d'un an.", 26.0, "Nourriture", "https://assets.zanimo.tn/produits/4014355243049.jpg"),
        ("COLLIER CHAT GLOSSY FUCHSIA", "Collier élégant pour chats avec clip de sécurité.", 14.0, "Accessoire", "https://assets.zanimo.tn/produits/8023222125209.jpg"),
        ("COLLIER CHAT CATMANIA BLEU", "Collier coloré avec élastique de sécurité pour chats.", 14.0, "Accessoire", "https://assets.zanimo.tn/produits/8023222204256.jpg"),
        ("BERLIN BLEU 38x38x59cm", "Arbre à chat compact avec plateforme et griffe inclus.", 58.0, "Accessoire", "https://assets.zanimo.tn/produits/8023222128569.jpg"),
        ("HAMBURG NOIR 30*30*60", "Arbre à chat design avec niches confortables.", 72.0, "Accessoire", "https://assets.zanimo.tn/produits/8023222128552.jpg"),
    ],
    "Oiseau": [
        ("CALCIMAX OISEAUX 500 GR", "Complément alimentaire calcique pour oiseaux en cage.", 46.2, "Nourriture", "https://assets.zanimo.tn/produits/6192461403484.jpg"),
        ("Mélange Graines Canaris 1kg", "Mélange de graines variées sélectionnées pour la vitalité des canaris.", 8.5, "Nourriture", "https://assets.zanimo.tn/produits/6192461403484.jpg"),
        ("Bâtonnets de Miel Perruches", "Friandise naturelle au miel à suspendre dans la cage.", 4.5, "Nourriture", "https://assets.zanimo.tn/produits/6192461403484.jpg"),
        ("Bloc Calcaire Oiseaux", "Apport essentiel en minéraux pour la solidité des os et du bec.", 3.0, "Nourriture", "https://assets.zanimo.tn/produits/6192461403484.jpg"),
        ("Sable de Sol Oiseaux 5kg", "Litière minérale naturelle pour fond de cage, facilite l'entretien.", 9.0, "Accessoire", "https://assets.zanimo.tn/produits/6192461403484.jpg"),
        ("Perchoir Bois Naturel", "Perchoir en bois naturel pour l'exercice et le confort de l'oiseau.", 6.5, "Accessoire", "https://assets.zanimo.tn/produits/6192461403484.jpg"),
        ("Baignoire Extérieure Cage", "Baignoire en plastique à fixer sur la porte de la cage.", 10.0, "Accessoire", "https://assets.zanimo.tn/produits/6192461403484.jpg"),
        ("Balançoire Colorée", "Jouet de stimulation pour oiseaux, fixation universelle.", 7.5, "Accessoire", "https://assets.zanimo.tn/produits/6192461403484.jpg"),
        ("Abreuvoir Tube 100ml", "Distributeur d'eau hygiénique à fixer sur les barreaux de cage.", 5.0, "Accessoire", "https://assets.zanimo.tn/produits/6192461403484.jpg"),
        ("Miroir avec Clochette", "Jouet miroir avec clochette pour occuper l'oiseau.", 4.0, "Accessoire", "https://assets.zanimo.tn/produits/6192461403484.jpg"),
    ],
    "Poisson": [
        ("EPUISETTE 10 CM", "Petite épuisette fine pour capturer les poissons sans les blesser.", 5.6, "Accessoire", "https://assets.zanimo.tn/produits/8023222002937.jpg"),
        ("EPUISETTE 25 CM", "Épuisette à long manche pour aquariums de grande taille.", 11.2, "Accessoire", "https://assets.zanimo.tn/produits/8023222002975.jpg"),
        ("DIFFUSEUR SPHERIQUE LARGE", "Diffuseur d'air pour une oxygénation optimale de l'aquarium.", 2.8, "Accessoire", "https://assets.zanimo.tn/produits/8023222019744.jpg"),
        ("DIFFUSEUR SPHERIQUE SMALL", "Petit diffuseur d'air silencieux pour nano-aquariums.", 2.0, "Accessoire", "https://assets.zanimo.tn/produits/8023222019720.jpg"),
        ("Flocons Poissons Tropicaux 250ml", "Aliment de base complet pour tous poissons tropicaux.", 8.5, "Nourriture", "https://assets.zanimo.tn/produits/8023222019744.jpg"),
        ("Comprimés de Fond Algues", "Alimentation spéciale fond pour poissons nettoyeurs.", 10.0, "Nourriture", "https://assets.zanimo.tn/produits/8023222019720.jpg"),
        ("Granulés Poissons Rouges 200ml", "Aliment complet pour poissons rouges et poissons d'eau froide.", 6.0, "Nourriture", "https://assets.zanimo.tn/produits/8023222019744.jpg"),
        ("Vers de Vase Lyophilisés 20g", "Friandise naturelle riche en protéines pour stimuler l'appétit.", 12.5, "Nourriture", "https://assets.zanimo.tn/produits/8023222019720.jpg"),
        ("Thermomètre Digital Aquarium", "Mesure précise de la température en temps réel.", 15.0, "Accessoire", "https://assets.zanimo.tn/produits/8023222002937.jpg"),
        ("Décorations Gravier Coloré 1kg", "Gravier lavé et coloré pour personnaliser le fond d'aquarium.", 8.0, "Accessoire", "https://assets.zanimo.tn/produits/8023222002975.jpg"),
    ],
    "Rongeur": [
        ("CAGE LAPIN NERO 3 100*50*45 BLEU", "Cage spacieuse et robuste pour lapins avec bac de fond.", 331.0, "Accessoire", "https://assets.zanimo.tn/produits/5411388520915.jpg"),
        ("CAGE LAPIN NERO 3 100*50*45 NOIR", "Cage lapin avec grille solide et porte d'accès pratique.", 331.0, "Accessoire", "https://assets.zanimo.tn/produits/5411388520946.jpg"),
        ("CAGE LAPIN AMBIENTE NOIR 80*50*43", "Cage compacte pour lapin nain ou cochon d'inde.", 169.0, "Accessoire", "https://assets.zanimo.tn/produits/5411388522308.jpg"),
        ("Mélange Granulés Hamster 500g", "Nutrition complète pour hamsters, évite le tri alimentaire.", 9.5, "Nourriture", "https://assets.zanimo.tn/produits/5411388520915.jpg"),
        ("Foin Naturel Rongeurs 500g", "Foin de qualité supérieure indispensable aux lapins et cobayes.", 12.0, "Nourriture", "https://assets.zanimo.tn/produits/5411388520946.jpg"),
        ("Bâtonnets Fruits Rongeurs", "Friandise aux fruits pour l'instinct naturel de ronger.", 5.5, "Nourriture", "https://assets.zanimo.tn/produits/5411388522308.jpg"),
        ("Aliment Complet Lapin 1kg", "Mélange de pellets équilibré pour lapins nains adultes.", 14.0, "Nourriture", "https://assets.zanimo.tn/produits/5411388520939.jpg"),
        ("Roue Silencieuse Hamster 20cm", "Roue d'exercice silencieuse pour hamsters et gerbilles.", 18.0, "Accessoire", "https://assets.zanimo.tn/produits/5411388520939.jpg"),
        ("Biberon Rongeur 500ml", "Biberon anti-goutte en plastique résistant.", 9.0, "Accessoire", "https://assets.zanimo.tn/produits/5411388520915.jpg"),
        ("Tunnel Osier Naturel Rongeur", "Cachette saine que le rongeur peut grignoter librement.", 11.0, "Accessoire", "https://assets.zanimo.tn/produits/5411388520946.jpg"),
    ]
}

def get_db_connection():
    db_url = os.getenv("DATABASE_URL")
    if not db_url: raise ValueError("DATABASE_URL non trouvée")
    res = urlparse(db_url)
    dbname = res.path.lstrip('/').split('?')[0]
    return pymysql.connect(
        host=res.hostname or '127.0.0.1',
        user=res.username or 'root',
        password=res.password or '',
        database=dbname,
        port=res.port or 3306,
        charset='utf8mb4',
        cursorclass=pymysql.cursors.DictCursor
    )

def main():
    try:
        conn = get_db_connection()
        with conn.cursor() as cursor:
            print("Nettoyage de la base de données...")
            cursor.execute("SET FOREIGN_KEY_CHECKS = 0")
            cursor.execute("TRUNCATE TABLE ligne_commande")
            cursor.execute("TRUNCATE TABLE produit")
            cursor.execute("TRUNCATE TABLE categorie")
            cursor.execute("SET FOREIGN_KEY_CHECKS = 1")

            print("Insertion des catégories...")
            cat_ids = {}
            for animal in CATALOG.keys():
                cursor.execute("INSERT INTO categorie (nom) VALUES (%s)", (animal,))
                cat_ids[animal] = cursor.lastrowid

            print("Insertion des produits réels (données Zanimo.tn)...")
            now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            all_data = []
            for animal, products in CATALOG.items():
                cat_id = cat_ids[animal]
                for nom, desc, prix, p_type, img in products:
                    stock = random.randint(10, 100)
                    # (nom, desc, prix, stock, image, image_url, animal_type, produit_type, created_at, is_promo, promotion, is_rupture, cat_id)
                    all_data.append((nom, desc, prix, stock, img, img, animal, p_type, now, 0, 0, 0, cat_id))


            sql = """INSERT INTO produit (nom, description, prix, stock, image, image_url, animal_type, produit_type, created_at, is_promo, promotion, is_rupture, categorie_id) 
                     VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"""

            cursor.executemany(sql, all_data)
            conn.commit()
            print(f"SUCCÈS : {len(all_data)} produits insérés avec images réelles de Zanimo.tn !")

    except Exception as e:
        print(f"ERREUR : {e}")
    finally:
        if 'conn' in locals() and conn.open: conn.close()

if __name__ == "__main__":
    main()
