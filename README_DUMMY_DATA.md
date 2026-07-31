# Foglalási rendszer API - Teszt adatok és hívások

Ez a dokumentum tartalmazza azokat a `curl` hívásokat, amikkel fel lehet tölteni az adatbázist és tesztelni az API végpontjait.
A hívások feltételezik, hogy a rendszer tiszta (`php artisan migrate:fresh` után) és a helyi szerver a `http://127.0.0.1:8000` címen fut.

## 1. Orvosok létrehozása (Doctors)

```bash
# Orvos 1: Teszt Béla
curl -s -X POST http://127.0.0.1:8000/api/v1/doctors \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name": "Teszt Béla", "email": "teszt.bela@example.com", "specialty": "Kardiológus"}'

# Orvos 2: Teszt Anna
curl -s -X POST http://127.0.0.1:8000/api/v1/doctors \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name": "Teszt Anna", "email": "teszt.anna@example.com", "specialty": "Bőrgyógyász"}'

# Orvos 3: Teszt Gábor
curl -s -X POST http://127.0.0.1:8000/api/v1/doctors \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name": "Teszt Gábor", "email": "teszt.gabor@example.com", "specialty": "Neurológus"}'

# Orvos 4: Teszt Zsófia
curl -s -X POST http://127.0.0.1:8000/api/v1/doctors \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name": "Teszt Zsófia", "email": "teszt.zsofia@example.com", "specialty": "Szemész"}'
```

## 2. Páciensek létrehozása (Patients)

```bash
# Páciens 1: Teszt Károly
curl -s -X POST http://127.0.0.1:8000/api/v1/patients \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name": "Teszt Károly", "email": "teszt.karoly@example.com", "phone": "+36301234567"}'

# Páciens 2: Teszt Júlia
curl -s -X POST http://127.0.0.1:8000/api/v1/patients \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name": "Teszt Júlia", "email": "teszt.julia@example.com", "phone": "+36209876543"}'

# Páciens 3: Teszt István
curl -s -X POST http://127.0.0.1:8000/api/v1/patients \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name": "Teszt István", "email": "teszt.istvan@example.com", "phone": "+36307654321"}'

# Páciens 4: Teszt Mária
curl -s -X POST http://127.0.0.1:8000/api/v1/patients \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name": "Teszt Mária", "email": "teszt.maria@example.com", "phone": "+36701122334"}'
```

## 3. Rendelési idők megadása (Availabilities)

```bash
# Teszt Béla (ID: 1) rendelési ideje (Aug 1. 08:00 - 12:00, 30 perces slotok)
curl -s -X POST http://127.0.0.1:8000/api/v1/doctors/1/availabilities \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"starts_at": "2026-08-01 08:00:00", "ends_at": "2026-08-01 12:00:00", "slot_duration": 30}'

# Teszt Anna (ID: 2) rendelési ideje (Aug 2. 10:00 - 14:00, 60 perces slotok)
curl -s -X POST http://127.0.0.1:8000/api/v1/doctors/2/availabilities \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"starts_at": "2026-08-02 10:00:00", "ends_at": "2026-08-02 14:00:00", "slot_duration": 60}'

# Teszt Gábor (ID: 3) rendelési ideje (Aug 3. 09:00 - 13:00, 20 perces slotok)
curl -s -X POST http://127.0.0.1:8000/api/v1/doctors/3/availabilities \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"starts_at": "2026-08-03 09:00:00", "ends_at": "2026-08-03 13:00:00", "slot_duration": 20}'

# Teszt Zsófia (ID: 4) rendelési ideje (Aug 4. 14:00 - 18:00, 30 perces slotok)
curl -s -X POST http://127.0.0.1:8000/api/v1/doctors/4/availabilities \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"starts_at": "2026-08-04 14:00:00", "ends_at": "2026-08-04 18:00:00", "slot_duration": 30}'
```

## 4. Szabad slotok lekérdezése (Free Slots)

```bash
# Lekérjük az összes elérhető slotot
curl -s -X GET http://127.0.0.1:8000/api/v1/free-slots \
  -H "Accept: application/json"
```

## 5. Foglalások létrehozása (Appointments)

```bash
# Teszt Károly (1) foglal Teszt Bélához (1)
curl -s -X POST http://127.0.0.1:8000/api/v1/appointments \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"patient_id": 1, "doctor_id": 1, "start_time": "2026-08-01 08:30:00", "end_time": "2026-08-01 09:00:00"}'

# Teszt Júlia (2) foglal Teszt Annához (2)
curl -s -X POST http://127.0.0.1:8000/api/v1/appointments \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"patient_id": 2, "doctor_id": 2, "start_time": "2026-08-02 11:00:00", "end_time": "2026-08-02 12:00:00"}'

# Teszt István (3) foglal Teszt Gáborhoz (3)
curl -s -X POST http://127.0.0.1:8000/api/v1/appointments \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"patient_id": 3, "doctor_id": 3, "start_time": "2026-08-03 09:20:00", "end_time": "2026-08-03 09:40:00"}'

# Teszt Mária (4) foglal Teszt Zsófiához (4)
curl -s -X POST http://127.0.0.1:8000/api/v1/appointments \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"patient_id": 4, "doctor_id": 4, "start_time": "2026-08-04 15:00:00", "end_time": "2026-08-04 15:30:00"}'
```

## 6. Státuszváltások tesztelése

```bash
# Károly foglalásának (ID: 1) megerősítése
curl -s -X PATCH http://127.0.0.1:8000/api/v1/appointments/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"status": "confirmed"}'

# Károly vizitjének lezárása (completed)
curl -s -X PATCH http://127.0.0.1:8000/api/v1/appointments/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"status": "completed"}'
  
# István foglalásának (ID: 3) lemondása (cancelled)
curl -s -X PATCH http://127.0.0.1:8000/api/v1/appointments/3 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"status": "cancelled", "cancellation_reason": "Páciens nem ér rá"}'
```

## 7. Törlés tesztelése (Javított 200 OK válasszal)

```bash
# Létrehozunk egy törlendő doktort
curl -s -X POST http://127.0.0.1:8000/api/v1/doctors \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name": "Teszt Törlendő", "email": "teszt.torlendo@example.com", "specialty": "Törlendő"}'

# Letöröljük a doktort (vissza kell adnia: 200 OK + "Doctor deleted successfully")
curl -s -X DELETE http://127.0.0.1:8000/api/v1/doctors/5 \
  -H "Accept: application/json"
```

## 8. Hibás (érvénytelen) hívások tesztelése (Validációk)

Ezek a hívások szándékosan hibásak, hogy ellenőrizzük az üzleti logikát és a validációt (422 Unprocessable Entity-t kell adniuk).

```bash
# 8.1. Rendelési idő a múltban (Kezdő időpont múltbéli)
curl -s -X POST http://127.0.0.1:8000/api/v1/doctors/1/availabilities \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"starts_at": "2020-01-01 08:00:00", "ends_at": "2020-01-01 12:00:00", "slot_duration": 30}'

# 8.2. Rendelési idők átfedése ugyanannál az orvosnál (Teszt Béla Aug 1. 08:00-12:00 már foglalt, így 10:00-14:00 érvénytelen)
curl -s -X POST http://127.0.0.1:8000/api/v1/doctors/1/availabilities \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"starts_at": "2026-08-01 10:00:00", "ends_at": "2026-08-01 14:00:00", "slot_duration": 30}'

# 8.3. Minimum 30 perces slot megsértése (15 perc)
curl -s -X POST http://127.0.0.1:8000/api/v1/doctors/1/availabilities \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"starts_at": "2026-08-05 08:00:00", "ends_at": "2026-08-05 12:00:00", "slot_duration": 15}'

# 8.4. Foglalás foglalt slotra (Károly már lefoglalta Aug 1. 08:30-09:00-t Teszt Bélánál, próbáljuk meg újra)
curl -s -X POST http://127.0.0.1:8000/api/v1/appointments \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"patient_id": 2, "doctor_id": 1, "start_time": "2026-08-01 08:30:00", "end_time": "2026-08-01 09:00:00"}'

# 8.5. Foglalás a múltba
curl -s -X POST http://127.0.0.1:8000/api/v1/appointments \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"patient_id": 2, "doctor_id": 1, "start_time": "2020-08-01 08:30:00", "end_time": "2020-08-01 09:00:00"}'

# 8.6. Páciens ütközése (Károly már foglalt Aug 1. 08:30-09:00-ra Teszt Bélánál, ne foglalhasson Teszt Annához sem ugyanekkor)
# Teszt Annának kell egy új slot erre az időre, hogy Károly megpróbálhasson oda is foglalni
curl -s -X POST http://127.0.0.1:8000/api/v1/doctors/2/availabilities \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"starts_at": "2026-08-01 08:00:00", "ends_at": "2026-08-01 12:00:00", "slot_duration": 30}'
# Károly megpróbál foglalni
curl -s -X POST http://127.0.0.1:8000/api/v1/appointments \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"patient_id": 1, "doctor_id": 2, "start_time": "2026-08-01 08:30:00", "end_time": "2026-08-01 09:00:00"}'
```
