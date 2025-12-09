
---

# ADMIN

## UC-A1 — Admin navigeert naar Eigenaren
| Veld | Inhoud |
|---|---|
| **Actor** | Admin |
| **Preconditie** | Admin is ingelogd; menu zichtbaar. |
| **Scenario** | 1. Klik **Menu**.<br>2. Klik **Beheer**.<br>3. Klik **Eigenaren**. |
| **Uitzonderingen** | — |
| **NFE** | Navigatie < 1 s. |
| **Postconditie** | Lijst met eigenaren is zichtbaar. |

## UC-A2 — Admin start “Nieuwe eigenaar”
| Veld | Inhoud |
|---|---|
| **Actor** | Admin |
| **Preconditie** | UC-A1 Postconditie. |
| **Scenario** | 1. Klik **Nieuwe eigenaar**. |
| **Uitzonderingen** | — |
| **NFE** | Formulier opent < 500 ms. |
| **Postconditie** | Formulier “Nieuwe eigenaar” is zichtbaar. |

## UC-A3 — Admin voert unieke e‑mail in voor nieuwe eigenaar
| Veld | Inhoud |
|---|---|
| **Actor** | Admin |
| **Preconditie** | UC-A2 Postconditie. |
| **Scenario** | 1. Klik in veld **E‑mail**. <br>2. Typ **geldige e‑mail**. <br>3. Verlaat veld (blur) of klik **Volgende**. |
| **Uitzonderingen** | (stap 2) Ongeldig e‑mailformaat → toon melding “Ongeldig e‑mailadres” → **terug naar stap 1**. <br>(stap 3) E‑mail bestaat al → toon melding “E‑mail al geregistreerd” → **terug naar stap 1**. |
| **NFE** | Back-end uniqueness check < 500 ms. |
| **Postconditie** | Veld E‑mail is geldig en uniek. |

## UC-A4 — Admin voert hotelnaam in voor nieuwe eigenaar
| Veld | Inhoud |
|---|---|
| **Actor** | Admin |
| **Preconditie** | UC-A2 Postconditie. |
| **Scenario** | 1. Klik in veld **Hotelnaam**. <br>2. Typ hotelnaam (min 2 tekens). |
| **Uitzonderingen** | (stap 2) Leeg / te kort → toon melding “Vul een geldige naam in” → **terug naar stap 1**. |
| **NFE** | Validatie direct. |
| **Postconditie** | Hotelnaam staat ingevuld. |

## UC-A5 — Admin verstuurt uitnodiging eigenaar
| Veld | Inhoud |
|---|---|
| **Actor** | Admin |
| **Preconditie** | UC-A3 en UC-A4 Postconditie. |
| **Scenario** | 1. Klik **Uitnodiging versturen**. |
| **Uitzonderingen** | (stap 1) E‑mailservice faalt → toon melding “Versturen mislukt, probeer later opnieuw” → **beëindig** (Rollback). |
| **NFE** | E‑mail binnen 1 minuut verzonden. |
| **Postconditie** | Pending eigenaar + hotelrecord aangemaakt; activatielink verzonden. |

## UC-A6 — Admin koppelt hotel aan bestaande eigenaar
| Veld | Inhoud |
|---|---|
| **Actor** | Admin |
| **Preconditie** | UC-A1 Postconditie; eigenaar bestaat; hotel bestaat of kan aangemaakt worden. |
| **Scenario** | 1. Klik **Eigenaar openen**. <br>2. Klik **Hotel koppelen**. <br>3. Kies bestaand hotel of klik **Nieuw hotel**. <br>4. Voer **Hotelnaam** in (min 2 tekens). <br>5. Klik **Opslaan/Koppelen**. |
| **Uitzonderingen** | (stap 4) Lege of te korte naam → melding → **terug naar stap 4**. <br>(stap 5) Reeds gekoppeld (MVP 1:1) → melding “Eigenaar heeft al een hotel” → **beëindig**. |
| **NFE** | Auditlog. |
| **Postconditie** | Eigenaar ↔ hotel relatie staat vast. |

## UC-A7 — Admin deactiveert eigenaar
| Veld | Inhoud |
|---|---|
| **Actor** | Admin |
| **Preconditie** | Eigenaar is actief. |
| **Scenario** | 1. Open **Eigenaar**. <br>2. Klik **Deactiveren**. <br>3. Lees waarschuwing. <br>4. Klik **Bevestigen**. |
| **Uitzonderingen** | (stap 4) Annuleert → **beëindig** (geen wijziging). |
| **NFE** | Direct effectief; auditlog. |
| **Postconditie** | Eigenaarstatus = gedeactiveerd; kan niet inloggen. |

## UC-A8 — Admin activeert eigenaar
| Veld | Inhoud |
|---|---|
| **Actor** | Admin |
| **Preconditie** | Eigenaar is gedeactiveerd. |
| **Scenario** | 1. Open **Eigenaar**. <br>2. Klik **Activeren**. <br>3. Klik **Bevestigen**. |
| **Uitzonderingen** | (stap 3) Annuleert → **beëindig**. |
| **NFE** | Direct effectief; auditlog. |
| **Postconditie** | Eigenaarstatus = actief. |

---

# OWNER / CO‑OWNER — SCHOONMAKERS

## UC-O1 — Owner navigeert naar Schoonmakers
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | Ingelogd. |
| **Scenario** | 1. Klik **Menu**. <br>2. Klik **Beheer**. <br>3. Klik **Schoonmakers**. |
| **Uitzonderingen** | — |
| **NFE** | — |
| **Postconditie** | Lijst schoonmakers zichtbaar. |

## UC-O2 — Owner start “Nieuwe schoonmaker”
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | UC-O1 Postconditie. |
| **Scenario** | 1. Klik **Nieuwe schoonmaker**. |
| **Uitzonderingen** | — |
| **NFE** | — |
| **Postconditie** | Formulier zichtbaar. |

## UC-O3 — Owner voert unieke e‑mail in (schoonmaker)
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | UC-O2 Postconditie. |
| **Scenario** | 1. Klik in **E‑mail**. <br>2. Typ **geldige e‑mail**. <br>3. Verlaat veld of klik **Volgende**. |
| **Uitzonderingen** | (stap 2) Ongeldig format → melding → **terug naar stap 1**. <br>(stap 3) E‑mail bestaat al (binnen hotel) → melding → **terug naar stap 1**. |
| **NFE** | Uniek binnen hotel. |
| **Postconditie** | Geldige, unieke e‑mail staat ingevuld. |

## UC-O4 — Owner vult naam in (schoonmaker)
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | UC-O2 Postconditie. |
| **Scenario** | 1. Klik in **Naam**. <br>2. Typ volledige naam (min 2 tekens). |
| **Uitzonderingen** | (stap 2) Leeg/te kort → melding → **terug naar stap 1**. |
| **NFE** | — |
| **Postconditie** | Naam staat ingevuld. |

## UC-O5 — Owner slaat schoonmaker op
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | UC-O3 en UC-O4 Postconditie. |
| **Scenario** | 1. Klik **Opslaan**. |
| **Uitzonderingen** | (stap 1) Serverfout → melding “Opslaan mislukt” → **beëindig** (Rollback). |
| **NFE** | Auditlog. |
| **Postconditie** | Schoonmaker = actief; zichtbaar in lijst. |

## UC-O6 — Owner deactiveert schoonmaker
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | Schoonmaker is actief. |
| **Scenario** | 1. Open schoonmaker. <br>2. Klik **Deactiveren**. <br>3. Klik **Bevestigen**. |
| **Uitzonderingen** | (stap 3) Annuleert → **beëindig**. |
| **NFE** | Herplanning binnen 5 s. |
| **Postconditie** | Schoonmaker gedeactiveerd en niet toewijsbaar. |

---

# OWNER / CO‑OWNER — KAMERS

## UC-R1 — Owner navigeert naar Kamers
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | Ingelogd. |
| **Scenario** | 1. Klik **Menu**. <br>2. Klik **Beheer**. <br>3. Klik **Kamers**. |
| **Uitzonderingen** | — |
| **NFE** | — |
| **Postconditie** | Lijst kamers zichtbaar. |

## UC-R2 — Owner start “Nieuwe kamer”
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | UC-R1 Postconditie. |
| **Scenario** | 1. Klik **Nieuwe kamer**. |
| **Uitzonderingen** | — |
| **NFE** | — |
| **Postconditie** | Formulier zichtbaar. |

## UC-R3 — Owner voert uniek kamernummer in
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | UC-R2 Postconditie. |
| **Scenario** | 1. Klik in **Kamernummer**. <br>2. Typ nummer. <br>3. Verlaat veld (blur). |
| **Uitzonderingen** | (stap 2/3) Nummer al in gebruik (binnen hotel) → melding → **terug naar stap 1**. <br>(stap 2) Leeg → melding → **terug naar stap 1**. |
| **NFE** | Uniek binnen hotel. |
| **Postconditie** | Kamernummer is valide en uniek. |

## UC-R4 — Owner kiest kamertype
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | UC-R2 Postconditie. |
| **Scenario** | 1. Open **Type** dropdown. <br>2. Kies kamertype. |
| **Uitzonderingen** | (stap 2) Geen keuze → melding → **terug naar stap 1**. |
| **NFE** | — |
| **Postconditie** | Kamertype staat ingevuld. |

## UC-R5 — Owner voert standaardduur (min) in
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | UC-R2 Postconditie. |
| **Scenario** | 1. Klik **Standaardduur (min)**. <br>2. Typ geheel getal ≥ 1. |
| **Uitzonderingen** | (stap 2) Ongeldig/≤0 → melding → **terug naar stap 1**. |
| **NFE** | — |
| **Postconditie** | Standaardduur staat ingevuld. |

## UC-R6 — Owner stelt vaste check‑out tijd in
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | UC-R2 Postconditie. |
| **Scenario** | 1. Klik **Check‑out**. <br>2. Kies tijd (hh:mm). |
| **Uitzonderingen** | (stap 2) Ongeldige tijd → melding → **terug naar stap 1**. |
| **NFE** | 24h/locale. |
| **Postconditie** | Check‑out staat ingevuld. |

## UC-R7 — Owner stelt vaste check‑in tijd in
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | UC-R2 Postconditie. |
| **Scenario** | 1. Klik **Check‑in**. <br>2. Kies tijd (hh:mm). |
| **Uitzonderingen** | (stap 2) Tijd ≤ check‑out → melding “Check‑in moet later zijn dan check‑out” → **terug naar stap 1**. |
| **NFE** | — |
| **Postconditie** | Check‑in staat ingevuld. |

## UC-R8 — Systeem berekent beschikbare tijd & waarschuwt bij overschrijding
| Veld | Inhoud |
|---|---|
| **Actor** | Systeem |
| **Preconditie** | UC-R5..R7 Postconditie. |
| **Scenario** | 1. Bereken **beschikbare tijd** = check‑in − check‑out. <br>2. Bereken **duur+buffer**. <br>3. Indien **duur+buffer > beschikbare tijd** → toon **Are you sure?** waarschuwing. |
| **Uitzonderingen** | — |
| **NFE** | On‑blur of bij **Opslaan**. |
| **Postconditie** | Waarschuwing getoond indien van toepassing. |

## UC-R9 — Owner slaat kamer op
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | UC-R3..R7 valide; UC-R8 evt. waarschuwing getoond. |
| **Scenario** | 1. Klik **Opslaan**. <br>2. (Indien waarschuwing actief) Klik **Doorgaan** of **Annuleren**. |
| **Uitzonderingen** | (stap 2) **Annuleren** → **beëindig** (Rollback). <br>(stap 1) Serverfout → melding → **beëindig**. |
| **NFE** | Auditlog. |
| **Postconditie** | Kamer aangemaakt of niet (bij annuleren/fout). |

## UC-R10 — Owner wijzigt kamer (idem als R3..R9 met herplanning)
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | Kamer bestaat. |
| **Scenario** | 1. Open kamer **Bewerken**. <br>2. Doorloop UC‑R3..R9. |
| **Uitzonderingen** | Zoals R3..R9. |
| **NFE** | Herplanning < 5 s. |
| **Postconditie** | Kamer bijgewerkt of niet. |

## UC-R11 — Owner verwijdert kamer
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | Kamer bestaat; geen lopende taak. |
| **Scenario** | 1. Klik **Verwijderen**. <br>2. Lees waarschuwing. <br>3. Klik **Bevestigen**. |
| **Uitzonderingen** | (stap 3) Lopende taak → melding → **beëindig**. <br>(stap 3) Annuleert → **beëindig**. |
| **NFE** | Soft delete in MVP. |
| **Postconditie** | Kamer verwijderd (soft). |

---

# OWNER / CO‑OWNER — PROBLEMEN & MOMENTEN

## UC-I1 — Owner meldt probleem bij kamer
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | Kamer bestaat. |
| **Scenario** | 1. Open **Problemen**. <br>2. Klik **Nieuw probleem**. <br>3. Kies **Kamer**. <br>4. Kies **Impact** (*geen haast* / *graag snel* / *kan niet gebruikt*). <br>5. (Optioneel) Typ notitie. <br>6. Klik **Opslaan**. |
| **Uitzonderingen** | (stap 3) Geen kamer gekozen → melding → **terug naar stap 3**. <br>(stap 4) Geen impact gekozen → melding → **terug naar stap 4**. |
| **NFE** | Bij impact “kan niet gebruikt”: herplanning direct. |
| **Postconditie** | Probleem aangemaakt (blokkeert planning indien van toepassing). |

## UC-I2 — Owner markeert probleem als “gefixt”
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | Probleem open. |
| **Scenario** | 1. Open probleem. <br>2. Klik **Markeer als gefixt**. <br>3. Klik **Bevestigen**. |
| **Uitzonderingen** | (stap 3) Annuleert → **beëindig**. |
| **NFE** | Herplanning direct. |
| **Postconditie** | Kamer weer planbaar. |

## UC-M1 — Owner plant schoonmaakmoment (datum)
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | Kamer bestaat. |
| **Scenario** | 1. Open **Momenten**. <br>2. Klik **Moment toevoegen**. <br>3. Kies **Datum**. <br>4. (Optioneel) Stel **extra venster** in (begin/eind). <br>5. Klik **Opslaan**. |
| **Uitzonderingen** | (stap 3) Datum in verleden → melding → **terug naar stap 3**. |
| **NFE** | Herplanning direct. |
| **Postconditie** | Moment opgeslagen. |

## UC-CAP1 — Owner stelt dagcapaciteit in
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | — |
| **Scenario** | 1. Open **Planning**. <br>2. Kies **Datum**. <br>3. Voer **Aantal schoonmakers** (≥0) in. <br>4. Klik **Opslaan**. |
| **Uitzonderingen** | (stap 3) Negatief/ongeldig → melding → **terug naar stap 3**. |
| **NFE** | Herplanning < 5 s. |
| **Postconditie** | Capaciteit ingesteld. |

## UC-PL1 — Owner start planner handmatig
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | Datum gekozen. |
| **Scenario** | 1. Klik **Plan nu**. |
| **Uitzonderingen** | (stap 1) Plannerfout → melding → **beëindig**. |
| **NFE** | Voltooid < 10 s. |
| **Postconditie** | Planning (her)berekend. |

---

# SYSTEEM — PLANNER & ALERTS

## UC-S1 — Planner plant automatisch (vandaag + morgen)
| Veld | Inhoud |
|---|---|
| **Actor** | Systeem |
| **Preconditie** | Kamers, momenten en capaciteit bestaan. |
| **Scenario** | 1. Scheduler triggert. <br>2. Filter kamers met **kan niet gebruikt**. <br>3. Bereken **duur+buffer**. <br>4. Toets tegen **vaste kamer‑tijden** en extra vensters. <br>5. Wijs 1 schoonmaker per kamer‑taak toe. <br>6. **Sequence** taken per schoonmaker (min. wachttijd). <br>7. Sla planning op. |
| **Uitzonderingen** | (stap 4) **duur+buffer > tijd** → maak **urgent probleem** + **e‑mail**; taak niet inplannen. <br>(stap 5) Capaciteit = 0 → waarschuwing naar eigenaar. |
| **NFE** | Per dag < 10 s. |
| **Postconditie** | Planning up‑to‑date. |

## UC-S2 — Planner herplant bij wijzigingen
| Veld | Inhoud |
|---|---|
| **Actor** | Systeem |
| **Preconditie** | Wijziging (moment/capaciteit/issue/kamer). |
| **Scenario** | 1. Event triggert herplanning. <br>2. Bereken deltas. <br>3. Verplaats/maak/annuleer taken. |
| **Uitzonderingen** | Onmogelijk binnen vensters → urgent probleem. |
| **NFE** | < 5 s. |
| **Postconditie** | Planning consistent. |

---

# SCHOONMAKER — TAKEN

## UC-C1 — Schoonmaker bekijkt “Mijn taken (vandaag)”
| Veld | Inhoud |
|---|---|
| **Actor** | Schoonmaker |
| **Preconditie** | Ingelogd; taken bestaan of niet. |
| **Scenario** | 1. Open app. |
| **Uitzonderingen** | — |
| **NFE** | Mobile‑first. |
| **Postconditie** | Lijst met taken of melding “Geen taken”. |

## UC-C2 — Schoonmaker opent taakdetails
| Veld | Inhoud |
|---|---|
| **Actor** | Schoonmaker |
| **Preconditie** | UC-C1 Postconditie; taak bestaat. |
| **Scenario** | 1. Tik op taak. |
| **Uitzonderingen** | — |
| **NFE** | — |
| **Postconditie** | Details zichtbaar. |

## UC-C3 — Schoonmaker start taak
| Veld | Inhoud |
|---|---|
| **Actor** | Schoonmaker |
| **Preconditie** | Taak niet gestart. |
| **Scenario** | 1. Tik **Start**. |
| **Uitzonderingen** | (stap 1) Netwerkfout → melding → **beëindig** (geen starttijd). |
| **NFE** | Exacte timestamp. |
| **Postconditie** | Taakstatus = bezig; starttijd gelogd. |

## UC-C4 — Schoonmaker stopt taak (pauze)
| Veld | Inhoud |
|---|---|
| **Actor** | Schoonmaker |
| **Preconditie** | UC-C3 Postconditie. |
| **Scenario** | 1. Tik **Stop**. |
| **Uitzonderingen** | (stap 1) Netwerkfout → melding → **beëindig** (blijft bezig). |
| **NFE** | Cumulatieve duur correct. |
| **Postconditie** | Stoptijd gelogd; status blijft “bezig/pauze”. |

## UC-C5 — Schoonmaker meldt taak klaar
| Veld | Inhoud |
|---|---|
| **Actor** | Schoonmaker |
| **Preconditie** | UC-C3 Postconditie. |
| **Scenario** | 1. Tik **Klaar melden**. |
| **Uitzonderingen** | (stap 1) Starttijd ontbreekt → melding → **beëindig**. <br>(stap 1) Netwerkfout → melding → **beëindig** (status blijft). |
| **NFE** | Eindtijd & werkelijke duur exact. |
| **Postconditie** | Status = klaar; werkelijke duur gelogd. |

## UC-C6 — Schoonmaker meldt probleem (foto optioneel)
| Veld | Inhoud |
|---|---|
| **Actor** | Schoonmaker |
| **Preconditie** | Taak bestaat. |
| **Scenario** | 1. Open taak → **Probleem melden**. <br>2. Typ notitie. <br>3. (Optioneel) Kies foto. <br>4. Tik **Verstuur**. |
| **Uitzonderingen** | (stap 3) Ongeldig bestandstype of >5 MB → melding → **terug naar stap 3**. <br>(stap 4) Netwerkfout → melding → **beëindig** (geen probleem aangemaakt). |
| **NFE** | Veilige upload. |
| **Postconditie** | Probleem aangemaakt; eigenaar notificatie. |

---

# RAPPORTAGE & TOEGANG

## UC-REP1 — Owner bekijkt dagoverzicht
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | Ingelogd. |
| **Scenario** | 1. Klik **Rapportage**. <br>2. Kies **Datum**. |
| **Uitzonderingen** | — |
| **NFE** | Query < 1 s. |
| **Postconditie** | Overzicht getoond (klaar/niet‑klaar, gepland/werkelijk). |

## UC-REP2 — Owner exporteert CSV
| Veld | Inhoud |
|---|---|
| **Actor** | Owner/Co‑Owner |
| **Preconditie** | Data voor periode beschikbaar. |
| **Scenario** | 1. Kies **Periode**. <br>2. Klik **CSV exporteren**. |
| **Uitzonderingen** | (stap 2) Geen data → melding → **beëindig**. |
| **NFE** | Correcte velden; download < 2 s. |
| **Postconditie** | CSV gedownload. |

## UC-SEC1 — Systeem dwingt RBAC af
| Veld | Inhoud |
|---|---|
| **Actor** | Systeem |
| **Preconditie** | Gebruiker is ingelogd of niet. |
| **Scenario** | 1. Gebruiker opent route. <br>2. Systeem controleert rol/rechten. |
| **Uitzonderingen** | Onvoldoende rechten → **403**; verlopen sessie → **Login**. |
| **NFE** | — |
| **Postconditie** | Alleen bevoegde toegang. |

## UC-AUD1 — Systeem schrijft auditlog
| Veld | Inhoud |
|---|---|
| **Actor** | Systeem |
| **Preconditie** | Kritieke actie (uitnodigen, (de)activeren, plannen, statuswijzigingen). |
| **Scenario** | 1. Schrijf record (user, actie, payload, timestamp). |
| **Uitzonderingen** | Logstorage fout → noodlog naar stderr/backup → **doorgaan** met hoofdactie. |
| **NFE** | Onveranderbaar; tijd in UTC. |
| **Postconditie** | Actie later te herleiden. |

---



