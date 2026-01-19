# Database Seeding Guide

## Important: Deploy Script Does NOT Wipe Database

The `scripts/deploy.sh` script runs `php artisan migrate --force` which:
- ✅ Only runs NEW migrations that haven't been executed yet
- ✅ Preserves all existing data
- ✅ Safe to run on production
- ❌ Does NOT run seeders automatically
- ❌ Does NOT wipe or reset the database

## Running the Seeder

### Locally
```bash
php artisan db:seed
```

### On Production
```bash
ssh u540587252@92.113.19.61 -p 65002
cd /home/u540587252/domains/housekeepr.nl/public_html
php artisan db:seed
```

## What Gets Seeded

### Users
- **1 Admin**: `admin@housekeepr.nl` (password: `password`)
- **3 Owners**:
  - `owner@housekeepr.nl` - Active owner with full hotel setup
  - `owner2@housekeepr.nl` - Active owner with smaller hotel
  - `newowner@housekeepr.nl` - Pending owner (hasn't completed account setup)
- **5 Cleaners**:
  - `cleaner1@housekeepr.nl` - Maria Janssen (active)
  - `cleaner2@housekeepr.nl` - Jan de Vries (active)
  - `cleaner3@housekeepr.nl` - Anna Bakker (active)
  - `cleaner4@housekeepr.nl` - Peter Smit (pending - hasn't logged in)
  - `cleaner5@housekeepr.nl` - Lisa van Dam (active, works at hotel 2)

All active users have password: `password`

### Hotels
- **Hotel Amsterdam Central** (owned by owner@housekeepr.nl)
  - 8 rooms: 101-103 (Standard), 201-203 (Deluxe), 301-302 (Suite)
  - 4 cleaners assigned
  - 10 bookings (past, current, and future)
  - 4 issues (3 open, 1 resolved)

- **Boutique Hotel Rotterdam** (owned by owner2@housekeepr.nl)
  - 5 rooms: 1-2 (Standard), 3-4 (Deluxe), 5 (Suite)
  - 1 cleaner assigned
  - 3 bookings

### Bookings
Variety of booking statuses:
- Past bookings (completed)
- Current stays (checked_in)
- Checking out today
- Upcoming bookings (confirmed)

### Issues
- 1 blocking maintenance issue (high priority)
- 2 open non-blocking issues (medium/low priority)
- 1 resolved issue

### Cleaning Tasks
- Automatically created for recent/upcoming checkouts
- Some assigned to cleaners, some pending

### Daily Capacity
- Set for 30 days (7 days in past, 23 days in future)
- Hotel 1: capacity of 3 cleanings per day
- Hotel 2: capacity of 1 cleaning per day

## Resetting the Database (DESTRUCTIVE)

⚠️ **WARNING**: This will DELETE ALL DATA!

### Locally
```bash
php artisan migrate:fresh --seed
```

### On Production (NOT RECOMMENDED)
```bash
# Backup first!
cp database/database.sqlite database/database.sqlite.backup.$(date +%Y%m%d_%H%M%S)

# Then reset (DESTRUCTIVE)
php artisan migrate:fresh --seed
```

## Notes

- The seeder uses `firstOrCreate()` so it's safe to run multiple times (won't create duplicates)
- However, running it multiple times will add MORE bookings and issues
- If you want a completely fresh database, use `migrate:fresh --seed` instead
- Production deployments automatically backup the database before extracting files

## Testing the Seeder

```bash
# See what will be seeded
php artisan db:seed --dry-run

# Actually seed the database
php artisan db:seed

# Check the results
php artisan tinker
>>> User::count()
>>> Hotel::count()
>>> Booking::count()
>>> Issue::count()
```

