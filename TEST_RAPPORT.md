# HouseKeepr Test Rapport

**Project:** HouseKeepr
**Datum:** 2026-01-26
**Versie:** MVP

---

## Inhoudsopgave

- [Admin Portal](#admin-portal)
- [Owner Portal - Schoonmakers](#owner-portal---schoonmakers)
- [Owner Portal - Kamers](#owner-portal---kamers)
- [Owner Portal - Boekingen](#owner-portal---boekingen)
- [Owner Portal - Problemen](#owner-portal---problemen)
- [Owner Portal - Planner](#owner-portal---planner)
- [Owner Portal - Dashboard](#owner-portal---dashboard)
- [Schoonmaker Portal](#schoonmaker-portal)
- [Planner & Alerts (Systeem)](#planner--alerts-systeem)
- [Rapportage & Toegang](#rapportage--toegang)
- [Should Have Features](#should-have-features)
- [Could Have Features](#could-have-features)

---

## Admin Portal

### Eigenaren Beheer

- [ ] **UC-A1** — Admin kan navigeren naar Eigenaren lijst
- [ ] **UC-A2** — Admin kan "Nieuwe eigenaar" formulier openen
- [ ] **UC-A3** — Admin kan unieke e-mail invoeren voor nieuwe eigenaar
  - [ ] Validatie: ongeldig e-mailformaat toont foutmelding
  - [ ] Validatie: bestaande e-mail toont "E-mail al geregistreerd"
- [ ] **UC-A4** — Admin kan hotelnaam invoeren (min 2 tekens)
  - [ ] Validatie: te kort/leeg toont foutmelding
- [ ] **UC-A5** — Admin kan uitnodiging versturen
  - [ ] E-mail met activatielink wordt verzonden
  - [ ] Pending eigenaar + hotel record wordt aangemaakt
  - [ ] Foutafhandeling bij e-mail service failure
- [ ] **UC-A6** — Admin kan hotel koppelen aan bestaande eigenaar
  - [ ] Kan bestaand hotel kiezen of nieuw hotel aanmaken
  - [ ] Validatie: eigenaar heeft al hotel (MVP 1:1)
- [ ] **UC-A7** — Admin kan eigenaar deactiveren
  - [ ] Bevestigingsdialoog wordt getoond
  - [ ] Gedeactiveerde eigenaar kan niet meer inloggen
  - [ ] Actie wordt gelogd in auditlog
- [ ] **UC-A8** — Admin kan eigenaar activeren
  - [ ] Actie wordt gelogd in auditlog

---

## Owner Portal - Schoonmakers

### Schoonmakers Beheer

- [ ] **UC-O1** — Owner kan navigeren naar Schoonmakers lijst
- [ ] **UC-O2** — Owner kan "Nieuwe schoonmaker" formulier openen
- [ ] **UC-O3** — Owner kan unieke e-mail invoeren voor schoonmaker
  - [ ] Validatie: ongeldig e-mailformaat
  - [ ] Validatie: e-mail al in gebruik binnen hotel
- [ ] **UC-O4** — Owner kan naam invoeren (min 2 tekens)
- [ ] **UC-O5** — Owner kan schoonmaker opslaan
  - [ ] Schoonmaker wordt actief en zichtbaar in lijst
  - [ ] Actie wordt gelogd
- [ ] **UC-O6** — Owner kan schoonmaker deactiveren
  - [ ] Bevestigingsdialoog
  - [ ] Herplanning wordt getriggerd
  - [ ] Schoonmaker niet meer toewijsbaar

### Schoonmaker Werkdagen

- [ ] Owner kan werkdagen instellen per schoonmaker (ma-zo)
- [ ] Werkdagen worden visueel weergegeven (7 cirkels)
- [ ] Wijzigingen triggeren herplanning

---

## Owner Portal - Kamers

### Kamers Beheer

- [ ] **UC-R1** — Owner kan navigeren naar Kamers lijst
- [ ] **UC-R2** — Owner kan "Nieuwe kamer" formulier openen
- [ ] **UC-R3** — Owner kan uniek kamernummer invoeren
  - [ ] Validatie: nummer al in gebruik binnen hotel
  - [ ] Validatie: leeg veld
- [ ] **UC-R4** — Owner kan kamertype kiezen
  - [ ] Dropdown met opties (single, double, suite, family)
- [ ] **UC-R5** — Owner kan standaardduur invoeren (minuten)
  - [ ] Validatie: geheel getal >= 1
- [ ] **UC-R6** — Owner kan vaste check-out tijd instellen
- [ ] **UC-R7** — Owner kan vaste check-in tijd instellen
  - [ ] Validatie: check-in moet later zijn dan check-out
- [ ] **UC-R8** — Systeem berekent beschikbare tijd & waarschuwt
  - [ ] "Are you sure?" bij duur+buffer > beschikbare tijd
- [ ] **UC-R9** — Owner kan kamer opslaan
  - [ ] Doorgaan/Annuleren bij waarschuwing
- [ ] **UC-R10** — Owner kan kamer wijzigen
  - [ ] Herplanning wordt getriggerd
- [ ] **UC-R11** — Owner kan kamer verwijderen
  - [ ] Bevestigingsdialoog
  - [ ] Blokkade bij lopende taak

---

## Owner Portal - Boekingen

### Boekingen Beheer

- [ ] Owner kan boekingen lijst bekijken
- [ ] Owner kan nieuwe boeking aanmaken
  - [ ] Kamer selecteren
  - [ ] Gastnaam invoeren
  - [ ] Check-in datum/tijd invoeren
  - [ ] Check-out datum/tijd invoeren
- [ ] Boeking aanmaken triggert automatisch schoonmaaktaak
- [ ] Owner kan boeking bewerken
- [ ] Owner kan boeking verwijderen
  - [ ] Bevestigingsdialoog

### Boekingen Weergave (Dashboard)

- [ ] Actieve boekingen sectie (ingecheckt, nog niet uitgecheckt)
- [ ] Vandaag sectie (check-in of check-out vandaag)
- [ ] Aankomend sectie (toekomstige check-ins)
- [ ] Oude boekingen (check-out < vandaag) worden automatisch verwijderd

---

## Owner Portal - Problemen

### Problemen Beheer

- [ ] **UC-I1** — Owner kan probleem melden bij kamer
  - [ ] Kamer selecteren
  - [ ] Impact kiezen: *geen haast* / *graag snel* / *kan niet gebruikt*
  - [ ] Optioneel: notitie toevoegen
  - [ ] Optioneel: foto uploaden
- [ ] **UC-I2** — Owner kan probleem markeren als "gefixt"
  - [ ] Bevestigingsdialoog
  - [ ] Kamer wordt weer planbaar
  - [ ] Herplanning wordt getriggerd
- [ ] "kan niet gebruikt" blokkeert planning voor kamer

### Problemen Weergave (Dashboard)

- [ ] Openstaand sectie (onopgeloste problemen van verleden)
- [ ] Vandaag sectie (problemen gemeld vandaag)
- [ ] Aankomend sectie (toekomstige problemen)
- [ ] Gefixte oude problemen worden automatisch verwijderd

---

## Owner Portal - Planner

- [ ] **UC-PL1** — Owner kan planner handmatig starten
  - [ ] "Plan Nu" knop
  - [ ] Planning wordt (her)berekend
  - [ ] Foutafhandeling bij plannerfout

### Schoonmaakplanning Weergave (Dashboard)

- [ ] Vandaag sectie (taken voor vandaag)
- [ ] Aankomend sectie (toekomstige taken)
- [ ] Oude taken (datum < vandaag) worden automatisch verwijderd
- [ ] Taak informatie: kamer, start, duur, eindtijd, deadline, schoonmaker, status

---

## Owner Portal - Dashboard

### Statistieken

- [ ] Totaal aantal kamers
- [ ] Taken vandaag
- [ ] Wachtende taken
- [ ] Open problemen

### Urgente Meldingen

- [ ] Urgente problemen (kan niet gebruikt) worden getoond
- [ ] Maximum 5 meest recente

### Accordion Secties

- [ ] Dashboard sectie (statistieken + urgente meldingen)
- [ ] Kamers sectie
- [ ] Boekingen sectie (actief/vandaag/aankomend)
- [ ] Schoonmakers sectie (met beschikbaarheid cirkels)
- [ ] Problemen sectie (openstaand/vandaag/aankomend)
- [ ] Schoonmaakplanning sectie (vandaag/aankomend)
- [ ] Prestaties sectie

---

## Schoonmaker Portal

### Mijn Taken

- [ ] **UC-C1** — Schoonmaker kan "Mijn taken (vandaag)" bekijken
  - [ ] Mobile-first interface
  - [ ] Melding "Geen taken" indien leeg
- [ ] **UC-C2** — Schoonmaker kan taakdetails openen
  - [ ] Kamer informatie
  - [ ] Tijdvenster (deadline)
  - [ ] Geplande duur
  - [ ] Status

### Taak Acties

- [ ] **UC-C3** — Schoonmaker kan taak starten
  - [ ] Starttijd wordt gelogd
  - [ ] Status wordt "in_progress"
  - [ ] Foutafhandeling bij netwerkfout
- [ ] **UC-C4** — Schoonmaker kan taak stoppen (pauze)
  - [ ] Stoptijd wordt gelogd
  - [ ] Cumulatieve duur correct berekend
- [ ] **UC-C5** — Schoonmaker kan taak klaar melden
  - [ ] Eindtijd wordt gelogd
  - [ ] Werkelijke duur wordt berekend
  - [ ] Status wordt "completed"

### Probleem Melden

- [ ] **UC-C6** — Schoonmaker kan probleem melden
  - [ ] Notitie invoeren (verplicht)
  - [ ] Foto uploaden (optioneel)
  - [ ] Validatie: bestandstype (jpg/png)
  - [ ] Validatie: bestandsgrootte (max 5MB)
  - [ ] Eigenaar krijgt notificatie

---

## Planner & Alerts (Systeem)

### Automatische Planning

- [ ] **UC-S1** — Planner plant automatisch bij nieuwe boeking
  - [ ] BookingCreated event triggert planning
  - [ ] Beschikbare schoonmakers ophalen (op basis van werkdagen)
  - [ ] Kamers met "kan niet gebruikt" worden gefilterd
  - [ ] Duur+buffer wordt berekend
  - [ ] Deadline = check-in tijd
  - [ ] 1 schoonmaker per taak toegewezen
  - [ ] Taken worden gesequenced per schoonmaker

### Herplanning

- [ ] **UC-S2** — Planner herplant bij wijzigingen
  - [ ] BookingCreated event
  - [ ] CleanerUpdated event (werkdagen/status)
  - [ ] IssueFixed event
  - [ ] RoomUpdated event

### Alerts

- [ ] Urgent probleem aangemaakt bij onvoldoende tijd
- [ ] E-mail naar eigenaar bij urgent probleem
- [ ] Waarschuwing bij geen beschikbare schoonmakers

### Planning Regels

- [ ] Buffertijd +10 minuten wordt toegevoegd
- [ ] Geen parallel werken op dezelfde kamer
- [ ] Wel parallel werken tussen verschillende kamers
- [ ] Wachttijd minimaliseren door starttijd verschuiven
- [ ] Niet plannen voor "kan niet gebruikt" kamers

---

## Rapportage & Toegang

### Dagoverzicht

- [ ] **UC-REP1** — Owner kan dagoverzicht bekijken
  - [ ] Datum selecteren
  - [ ] Klaar/niet-klaar taken
  - [ ] Gepland vs. werkelijk

### Export

- [ ] **UC-REP2** — Owner kan CSV exporteren
  - [ ] Periode selecteren
  - [ ] Alle taken en logs in CSV
  - [ ] Melding bij geen data

### Prestaties

- [ ] Prestatie overzicht per schoonmaker (laatste 7 dagen)
- [ ] Totaal taken voltooid
- [ ] Gepland vs. werkelijk tijd
- [ ] Sneller/langzamer/exact op tijd

### Beveiliging

- [ ] **UC-SEC1** — RBAC wordt afgedwongen
  - [ ] Admin alleen admin routes
  - [ ] Owner alleen owner routes (eigen hotel)
  - [ ] Schoonmaker alleen cleaner routes (eigen taken)
  - [ ] 403 bij onvoldoende rechten
  - [ ] Redirect naar login bij verlopen sessie

### Auditlog

- [ ] **UC-AUD1** — Kritieke acties worden gelogd
  - [ ] Uitnodigen eigenaar/schoonmaker
  - [ ] (De)activeren accounts
  - [ ] Planning wijzigingen
  - [ ] Status wijzigingen
  - [ ] Timestamp in UTC

---

## Should Have Features

- [ ] **Authed User rol** — Extra gebruikers (secretariaat) toegang tot owner portal
- [ ] **Advies standaardduur** — Bij structureel te lang/kort advies geven
- [ ] **E-mail notificaties** — Bij herplanning of urgent probleem
- [ ] **Dagelijks e-mailrapport** — Naar eigenaar/manager

---

## Could Have Features

- [ ] **PWA/offline** — Schoonmaker app offline beschikbaar
- [ ] **Analytics/trends dashboard** — Uitgebreide statistieken
- [ ] **Meertaligheid** — NL/EN/DE ondersteuning
- [ ] **API/webhooks** — Integratie mogelijkheden

---

## Won't Have (MVP)

- ~~Meerdere hotels onder één eigenaar (multi-tenant)~~
- ~~Licentiefacturatie/invoicing~~
- ~~Geavanceerde AI/optimizer~~
- ~~Live koppeling met booking-site~~

---

## Test Notities

| Datum | Tester | Sectie | Opmerkingen |
|-------|--------|--------|-------------|
|       |        |        |             |
|       |        |        |             |
|       |        |        |             |

---

**Totaal:** _____ / _____ tests geslaagd

**Getekend:**
Naam: ________________________
Datum: ________________________
