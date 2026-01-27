# Local Development Setup

## Installation

1. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

2. **Setup environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Setup database:**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   php artisan db:seed
   ```

4. **Build frontend assets:**
   ```bash
   npm run dev
   ```

5. **Start development server:**
   ```bash
   php artisan serve
   ```

## Available Test Scripts

- `scripts/test-local.sh` - Test the local API
- `scripts/test-api.sh` - Comprehensive API tests
- `scripts/seed.sh` - Seed database with demo data
- `scripts/reset-db.sh` - Reset database (migrate fresh + seed)

## Running Tests

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=BookingTest
```

## Default Users

After seeding, you can login with:

- **Admin:** `admin@housekeepr.nl` / `password`
- **Owner:** `owner1@housekeepr.nl` / `password`
- **Cleaner:** `cleaner1@housekeepr.nl` / `password`

### Issue: Database errors
## Troubleshooting

### Issue: "Database not found" errors
**Solution:**
```bash
# Check database exists and has correct permissions
ls -la database/database.sqlite
touch database/database.sqlite
chmod 664 database/database.sqlite

# Run migrations
php artisan migrate
```

### Issue: API returns 500 errors
**Solution:**
```bash
# Check logs
tail -50 storage/logs/laravel.log

# Clear all caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
```

### Issue: Frontend not loading/styles missing
**Solution:**
```bash
# Rebuild assets
npm run build

# Or run in watch mode during development
npm run dev
```

## API Endpoints

All API endpoints require authentication token (except /login):

- `POST /api/login` - Get authentication token
- `GET /api/user` - Get current user
- `GET /api/bookings` - List bookings
- `POST /api/bookings` - Create booking
- `GET /api/cleaning-tasks` - List tasks
- `POST /api/cleaning-tasks/{id}/start` - Start task
- `POST /api/cleaning-tasks/{id}/complete` - Complete task

See `API_TESTING.http` for complete API documentation.

