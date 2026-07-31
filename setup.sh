#!/bin/bash

# Színek a formázott kimenethez
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # Nincs szín

echo -e "${GREEN}===============================================${NC}"
echo -e "${GREEN}   Időpont-foglalási Rendszer - Telepítő Script${NC}"
echo -e "${GREEN}===============================================${NC}"

# 1. Composer függőségek ellenőrzése és telepítése
echo -e "\n${YELLOW}[1/7] Függőségek ellenőrzése és telepítése (Composer)...${NC}"
if ! command -v composer &> /dev/null; then
    echo -e "${RED}Hiba: A Composer nem található a rendszeren! Kérlek telepítsd.${NC}"
    exit 1
fi
composer install

# 2. NPM függőségek telepítése
echo -e "\n${YELLOW}[2/7] NPM függőségek telepítése...${NC}"
if ! command -v npm &> /dev/null; then
    echo -e "${RED}Hiba: Az NPM nem található a rendszeren! Kérlek telepítsd a Node.js-t.${NC}"
    exit 1
fi
npm install

# 3. Kliensoldali fájlok buildelése
echo -e "\n${YELLOW}[3/7] Kliensoldali fájlok buildelése (npm run build)...${NC}"
npm run build

# 4. .env fájl előkészítése
echo -e "\n${YELLOW}[4/7] Környezeti változók (.env) beállítása...${NC}"
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        echo -e "${GREEN}✓ .env fájl létrehozva az .env.example alapján.${NC}"
    else
        echo -e "${RED}Hiba: Nem található az .env.example fájl!${NC}"
        exit 1
    fi
else
    echo -e "✓ Az .env fájl már létezik, a lépés kihagyva."
fi

# Beállítjuk a SQLite adatbázist, ha nincs beállítva
# Megkeressük a DB_CONNECTION sort és sqlite-ra cseréljük
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env
echo -e "${GREEN}✓ Adatbázis kapcsolat beállítva: SQLite.${NC}"

# 5. Adatbázis fájl (SQLite) létrehozása
echo -e "\n${YELLOW}[5/7] SQLite adatbázis fájl előkészítése...${NC}"
DB_PATH="database/database.sqlite"
if [ ! -f "$DB_PATH" ]; then
    touch "$DB_PATH"
    echo -e "${GREEN}✓ Létrehozva: $DB_PATH${NC}"
else
    echo -e "✓ Az adatbázis fájl már létezik."
fi

# 6. Alkalmazás kulcs generálása
echo -e "\n${YELLOW}[6/7] Alkalmazás kulcs (App Key) generálása...${NC}"
php artisan key:generate
echo -e "${GREEN}✓ Kulcs sikeresen legenerálva.${NC}"

# 7. Adatbázis migrációk
echo -e "\n${YELLOW}[7/7] Adatbázis migrációk futtatása (táblák létrehozása)...${NC}"
# fresh: ledobja a meglévő táblákat (ha vannak) és újra lefuttatja a migrációkat
php artisan migrate:fresh --seed --force
echo -e "${GREEN}✓ Migrációk sikeresen lefutottak.${NC}"

# Opcionális seedelés ellenőrzés
# Ha van Seed, akkor érdemes lefuttatni. Ehhez megpróbáljuk futtatni, ha nincs seed, legfeljebb nem csinál semmit
# php artisan db:seed --force

echo -e "\n${GREEN}===============================================${NC}"
echo -e "${GREEN}✓ Telepítés és beállítás sikeresen befejeződött!${NC}"
echo -e "${GREEN}===============================================${NC}"

echo -e "\n${YELLOW}A fejlesztői szerver indításához futtasd az alábbi parancsot:${NC}"
echo -e "  php artisan serve\n"
