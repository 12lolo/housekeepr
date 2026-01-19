# HouseKeepr

Hotel cleaning and booking management system built with Laravel.

## Features

- 👥 Multi-role system (Admin, Owner, Cleaner)
- 🏨 Hotel and room management
- 📅 Booking and reservation tracking
- 🧹 Cleaning task assignment and scheduling
- 🔧 Issue/maintenance tracking
- 📊 Dashboard and reporting
- 🎨 Neumorphic UI design with dark mode support

## Quick Start

### Development

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate

# Seed with test data (optional)
./scripts/seed.sh
# OR manually:
php artisan db:seed

# Build assets
npm run dev

# Start server
php artisan serve --port=8000
```

Visit http://127.0.0.1:8000

### Database Scripts

#### Add Test Data (Safe - doesn't wipe data)
```bash
./scripts/seed.sh
# OR
php artisan db:seed
```

This adds:
- 1 Admin user
- 3 Owners (1 pending)
- 2 Hotels with rooms
- 5 Cleaners (1 pending)
- 13 Bookings
- 4 Issues
- Cleaning tasks

#### Reset Database (DESTRUCTIVE - wipes all data)
```bash
./scripts/reset-db.sh
# OR
php artisan migrate:fresh --seed
```

See [DATABASE_SEEDING.md](DATABASE_SEEDING.md) for details.

## Test Accounts

After seeding, use these credentials:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@housekeepr.nl | password |
| Owner | owner@housekeepr.nl | password |
| Owner | owner2@housekeepr.nl | password |
| Cleaner | cleaner1@housekeepr.nl | password |
| Cleaner | cleaner2@housekeepr.nl | password |

## Deployment

```bash
./scripts/deploy.sh
```

**Important**: The deploy script:
- ✅ Runs migrations (safe, doesn't wipe data)
- ✅ Backs up database automatically
- ❌ Does NOT run seeders
- ❌ Does NOT reset/wipe database

See [DEPLOYMENT.md](DEPLOYMENT.md) for details.

## Documentation

- [Deployment Guide](DEPLOYMENT.md)
- [Database Seeding](DATABASE_SEEDING.md)
- [Testing Guide](TESTING_GUIDE.md)
- [Use Cases](HouseKeepr_Usecases.md)

## Tech Stack

- Laravel 11
- SQLite
- Vite + SCSS
- Spatie Activity Log
- Spatie Laravel Permission

## License

Proprietary - All Rights Reserved

