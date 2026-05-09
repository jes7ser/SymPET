import os
import re
import pymysql
import random
from datetime import datetime
from urllib.parse import urlparse
from dotenv import load_dotenv
from playwright.sync_api import sync_playwright

load_dotenv(".env.local")
load_dotenv(".env")

CONFIG = {
    "Chien": "https://zanimo.tn/46-aliments-chiens",
    "Chat": "https://zanimo.tn/53-aliments-chats",
    "Oiseau": "https://zanimo.tn/62-aliments-oiseaux",
    "Poisson": "https://zanimo.tn/66-aliments-poissons",
    "Rongeur": "https://zanimo.tn/57-aliments-rongeurs"
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

def extract_price(text):
    match = re.search(r"(\d+[\d\s.,]*)", text)
    if not match: return 0.0
    val = match.group(1).replace(" ", "").replace(",", ".")
    try: return float(val)
    except: return 0.0

def scrape_category(page, animal_type):
    products = []
    url = CONFIG[animal_type]
    print(f"  [>] Navigation vers {url}...")
    try:
        # Navigation avec délai aléatoire et User-Agent humain
        page.goto(url, timeout=90000, wait_until="domcontentloaded")
        page.wait_for_timeout(random.randint(2000, 4000))
        
        # Fermer le pop-up boutique si présent
        btn_valider = page.query_selector("button:has-text('Valider')")
        if btn_valider:
            btn_valider.click()
            page.wait_for_timeout(1000)
            
        # Simuler un scroll humain pour déclencher le chargement
        page.mouse.wheel(0, 800)
        page.wait_for_timeout(1500)

        items = page.query_selector_all(".product-miniature")
        print(f"  [DEBUG] {len(items)} produits détectés")
        
        for item in items:
            try:
                name_el = item.query_selector(".title-product")
                if not name_el: continue
                name = name_el.inner_text().strip()
                
                p_type = "Nourriture" if any(x in name.upper() for x in ["ALIMENT", "CROQUETTE", "PÂTÉE", "FRIANDISE", "GRAINES"]) else "Accessoire"
                price = extract_price(item.inner_text())
                
                img_el = item.query_selector("img")
                img_url = img_el.get_attribute("src") if img_el else None
                
                if not img_url or "cart" in img_url or "logo" in img_url: continue

                products.append((name, f"Qualité premium pour {animal_type}", price, 50, img_url, animal_type, p_type, datetime.now()))
                if len(products) >= 8: break
            except: continue
    except Exception as e:
        print(f"  [!] Erreur : {e}")
    return products

def main():
    try:
        conn = get_db_connection()
        with sync_playwright() as p:
            # Lancement avec un User-Agent crédible
            browser = p.chromium.launch(headless=True)
            context = browser.new_context(
                user_agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
                viewport={"width": 1280, "height": 720}
            )
            page = context.new_page()
            
            all_products = []
            for animal in CONFIG.keys():
                print(f"Traitement {animal}...")
                all_products.extend(scrape_category(page, animal))
                page.wait_for_timeout(random.randint(1000, 2000)) # Pause entre catégories
            
            if not all_products:
                page.screenshot(path="final_debug.png")
            
            browser.close()

        if all_products:
            with conn.cursor() as cursor:
                print(f"\nNettoyage et insertion de {len(all_products)} produits...")
                cursor.execute("SET FOREIGN_KEY_CHECKS = 0")
                cursor.execute("TRUNCATE TABLE ligne_commande")
                cursor.execute("TRUNCATE TABLE produit")
                cursor.execute("SET FOREIGN_KEY_CHECKS = 1")
                
                sql = "INSERT INTO produit (nom, description, prix, stock, image_url, animal_type, produit_type, created_at) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"
                cursor.executemany(sql, all_products)
                conn.commit()
                print(f"SUCCÈS : {len(all_products)} produits importés de Zanimo.tn")
        else:
            print("ECHEC : Toujours aucun produit. Le site bloque peut-être l'IP du serveur.")

    except Exception as e:
        print(f"ERREUR FATALE : {e}")
    finally:
        if 'conn' in locals() and conn.open: conn.close()

if __name__ == "__main__":
    main()
