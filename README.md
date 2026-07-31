# Időpont-foglalási Rendszer API

Ez egy REST API egy orvosi rendelő időpont-foglalási rendszeréhez, amely Laravel keretrendszerben készült.

## 📋 Áttekintés

A rendszer lehetővé teszi orvosok és páciensek kezelését, orvosok rendelési idejének megadását, valamint a páciensek számára az elérhető (szabad) időpontokra történő foglalást. A foglalások egy teljes életciklust járnak be a létrehozástól (`pending`) a megerősítésen (`confirmed`) át a teljesítésig (`completed`) vagy lemondásig (`cancelled`).

### Főbb entitások:
- **Orvos (Doctor)**: Név, egyedi email, szakterület.
- **Páciens (Patient)**: Név, egyedi email, telefonszám.
- **Rendelési idő (Availability)**: Egy orvoshoz tartozó időablak, minimum 30 perces slotokkal.
- **Foglalás (Appointment)**: Páciens és Orvos közötti vizit, egyedi időponttal és állapotkezeléssel.

## 🛠 Technológiai Stack

- **PHP** 8.5
- **Laravel** 13
- **Adatbázis**: SQLite
- **API Formátum**: JSON

## 🚀 Telepítés és beállítás (Setup)

A projekt helyi környezetben történő elindításához használhatod az automatikus telepítő szkriptet, vagy követheted a manuális lépéseket.

### Automatikus telepítés (Ajánlott)

A gyökérkönyvtárban található egy `setup.sh` szkript, amely elvégzi a függőségek telepítését, a környezeti változók beállítását, az adatbázis létrehozását és a migrációkat (valamint a tesztadatok betöltését).

1. **Tároló klónozása** (ha még nem történt meg):
   ```bash
   git clone <repository-url>
   cd appointment-booking-system
   ```

2. **Jogosultság adása és futtatás**:
   ```bash
   chmod +x setup.sh
   ./setup.sh
   ```

Miután a szkript lefutott, indíthatod a lokális szervert (a 6. lépés szerint).

### Manuális telepítés

Ha inkább lépésről lépésre szeretnéd beállítani a projektet:

1. **Tároló klónozása** (ha még nem történt meg):
   ```bash
   git clone <repository-url>
   cd appointment-booking-system
   ```

2. **Függőségek telepítése**:
   ```bash
   composer install
   ```

3. **Környezeti változók beállítása**:
   Másold le a példa konfigurációs fájlt, majd állítsd be az adatbázis kapcsolatot (alapértelmezetten használhatsz SQLite-ot is):
   ```bash
   cp .env.example .env
   ```
   *Ha SQLite-ot használsz, hozd létre a fájlt: `touch database/database.sqlite`, és az `.env`-ben az adatbázis driver legyen `DB_CONNECTION=sqlite`.*

4. **Alkalmazás kulcs generálása**:
   ```bash
   php artisan key:generate
   ```

5. **Adatbázis migrációk futtatása**:
   Ez a parancs létrehozza a szükséges táblákat a rendszer számára.
   ```bash
   php artisan migrate:fresh
   ```
   *(A projekt tartalmaz Seedereket a tesztadatok betöltéséhez, futtathatod a `php artisan migrate:fresh --seed` parancsot is).*

6. **Lokális szerver indítása**:
   ```bash
   php artisan serve
   ```
   Az API alapesetben az alábbi címen lesz elérhető: `http://127.0.0.1:8000`

## 📖 API Dokumentáció (Swagger)

A projekt tartalmaz Swagger / OpenAPI specifikáció szerinti dokumentációt, amely jelentősen megkönnyíti az API felfedezését és tesztelését. 
Miután elindítottad a lokális szervert, a dokumentáció és az interaktív Swagger UI felület (az alapértelmezett beállítások szerint) jellemzően az alábbi végponton érhető el:
- `http://127.0.0.1:8000/docs/api`
*(Ezen a felületen azonnal kipróbálható az összes elérhető végpont.)*

## 🧪 Tesztelés

A projekt kritikus üzleti logikáira (pl. átfedések elkerülése, állapotgépek, validációk) Feature tesztek készültek.
A tesztek futtatásához használd a következő parancsot:
```bash
php artisan test
```

## 📝 Dummy Adatok és API Tesztelés

A manuális tesztelés megkönnyítése érdekében készítettünk egy külön fájlt, amely előre megírt API hívásokat tartalmaz. 
Ezek segítségével pillanatok alatt feltöltheted az adatbázist teszt entitásokkal (orvosokkal, páciensekkel, rendelési időkkel), és letesztelheted a foglalási folyamatokat anélkül, hogy az adatokat kézzel kellene kitalálnod.

Néhány példa a hívásokra:

**Orvos létrehozása:**
```bash
curl -s -X POST http://127.0.0.1:8000/api/v1/doctors \
  -H "Content-Type: application/json" \
  -d '{"name": "Teszt Béla", "email": "teszt.bela@example.com", "specialty": "Kardiológus"}'
```

**Szabad időpontok lekérdezése:**
```bash
curl -s -X GET http://127.0.0.1:8000/api/v1/free-slots \
  -H "Accept: application/json"
```

A teljes tesztadat-generáló folyamatot és az összes végpont (valamint a hibaesetek) tesztelését a **[README_DUMMY_DATA.md](README_DUMMY_DATA.md)** fájlban találod.

---
**Megjegyzés**: Az API publikus végpontokkal dolgozik.
