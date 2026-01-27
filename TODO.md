# TODO: Bug Fixes

Twee bugs moeten opgelost worden in de HouseKeepr applicatie.

---

## Bug #1: Probleem Melden Knop Werkt Niet

**Probleem:** De rode zwevende knop rechts onderaan in het schoonmaker dashboard doet niets bij klikken.

**Relevante Bestanden:**

- `resources/views/cleaner/dashboard.blade.php` (regel ~195-205)
  - Bevat de rode zwevende knop onderaan de pagina
  - De knop is een `<button>` element zonder navigatie functionaliteit

- `resources/views/cleaner/issues/create.blade.php`
  - Dit is de pagina waar schoonmakers problemen kunnen melden
  - Heeft een formulier voor het aanmaken van een issue

- `resources/views/admin/owners/index.blade.php` (regel ~140-170)
  - Voorbeeld van een werkende knop die naar een andere pagina navigeert
  - De "Nieuwe Eigenaar Toevoegen" knop

- `routes/web.php`
  - Bevat de route `cleaner.issues.create` die naar de issue formulier pagina gaat

**Testen:**
- Login: `cleaner1@housekeepr.nl` / `password`
- Klik op de rode knop rechts onderaan
- Verwacht: Navigatie naar het probleem melden formulier

---

## Bug #2: Onleesbare Hint Tekst

**Probleem:** De CSS class `neu-hint` heeft geen styling gedefinieerd, waardoor tekst zwart op grijs verschijnt en onleesbaar is.

**Relevante Bestanden:**

- `resources/scss/components/_forms.scss`
  - Bevat styling voor formulier elementen zoals `neu-label`, `neu-input`, etc.
  - De class `neu-hint` ontbreekt hier maar zou hier gedefinieerd moeten worden

- `resources/views/admin/owners/index.blade.php` (regel 162)
  - Gebruikt `<small class="neu-hint">` in de modal
  
- `resources/views/cleaner/issues/create.blade.php` (regel 81, 111)
  - Gebruikt `<small class="neu-hint">` bij formuliervelden

**Testen:**
- Run `npm run dev` na SCSS wijzigingen
- Login: `admin@housekeepr.nl` / `password`
- Ga naar "Eigenaren" → Klik "Nieuwe Eigenaar Toevoegen"
- Controleer of de hint tekst onder het email veld leesbaar is
