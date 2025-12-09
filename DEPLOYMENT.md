# Production Deployment Guide

## Pre-Deployment Checklist

1. **Run pre-deployment checks:**
   ```bash
   bash scripts/pre-deploy-check.sh
   ```

2. **Ensure you have:**
   - ✅ sshpass installed (`sudo pacman -S sshpass` or `sudo apt install sshpass`)
   - ✅ npm packages installed (`npm install`)
   - ✅ All tests passing (`php artisan test`)

## Deploy to Production

1. **Deploy the application:**
   ```bash
   bash scripts/deploy.sh
   ```

   This script will:
   - Build frontend assets (npm run build)
   - Create deployment package (excluding dev files)
   - Upload to production server
   - Extract files
   - Backup database
   - Run migrations
   - Clear all caches
   - Set correct permissions

2. **Check production status:**
   ```bash
   bash scripts/check-production.sh
   ```

## First-Time Production Setup

If this is your first deployment, you need to set up the environment:

1. **SSH into production:**
   ```bash
   ssh -p 65002 u540587252@92.113.19.61
   ```

2. **Navigate to project:**
   ```bash
   cd /home/u540587252/domains/housekeepr.nl/public_html
   ```

3. **Set up .env file:**
   ```bash
   # Copy the production template
   cp .env.production .env
   
   # Generate application key
   php artisan key:generate
   
   # Verify configuration
   cat .env | grep APP_KEY
   ```

4. **Set up database:**
   ```bash
   # Create database file
   touch database/database.sqlite
   chmod 664 database/database.sqlite
   
   # Run migrations
   php artisan migrate --force
   
   # Optional: Seed with demo data
   php artisan db:seed --force
   ```

5. **Set permissions:**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chmod 664 database/database.sqlite
   ```

## Testing the API on Production

1. **Test API structure:**
   ```bash
   bash scripts/test-api.sh
   ```

2. **Test with real credentials:**
   ```bash
   # Get auth token
   curl -X POST https://housekeepr.nl/api/login \
     -H "Content-Type: application/json" \
     -d '{"email":"your@email.com","password":"yourpassword"}' | jq
   
   # Use the token (replace YOUR_TOKEN)
   curl -H "Authorization: Bearer YOUR_TOKEN" \
     https://housekeepr.nl/api/bookings | jq
   ```

3. **Using the HTTP testing file:**
   - Open `API_TESTING.http` in VS Code or PhpStorm
   - Change `@baseUrl` to `https://housekeepr.nl/api`
   - Run requests directly from the IDE

## Troubleshooting

### Issue: "Class not found" errors
**Solution:**
```bash
ssh -p 65002 u540587252@92.113.19.61
cd /home/u540587252/domains/housekeepr.nl/public_html
php artisan optimize:clear
composer dump-autoload
```

### Issue: Database errors
**Solution:**
```bash
# Check database exists and has correct permissions
ls -la database/database.sqlite
chmod 664 database/database.sqlite

# Run migrations
php artisan migrate --force
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

### Issue: CORS errors
**Solution:**
Make sure `config/sanctum.php` has correct stateful domains:
```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1,housekeepr.nl,www.housekeepr.nl')),
```

## Production URLs

- **Website:** https://housekeepr.nl
- **API Base:** https://housekeepr.nl/api
- **Login:** https://housekeepr.nl/login
- **Admin:** https://housekeepr.nl/admin/dashboard
- **Owner:** https://housekeepr.nl/owner/dashboard
- **Cleaner:** https://housekeepr.nl/cleaner/dashboard

## API Endpoints

All API endpoints require authentication token (except /login):

- `POST /api/login` - Get authentication token
- `GET /api/user` - Get current user
- `GET /api/bookings` - List bookings
- `POST /api/bookings` - Create booking
- `GET /api/cleaning-tasks` - List tasks
- `POST /api/cleaning-tasks/{id}/start` - Start task
- `POST /api/cleaning-tasks/{id}/complete` - Complete task

## Monitoring

1. **Check application health:**
   ```bash
   curl https://housekeepr.nl/up
   ```

2. **Check logs:**
   ```bash
   ssh -p 65002 u540587252@92.113.19.61
   tail -f /home/u540587252/domains/housekeepr.nl/public_html/storage/logs/laravel.log
   ```

3. **Database backup:**
   ```bash
   # Backups are created automatically on each deployment
   # Located at: database/database.sqlite.backup.YYYYMMDD_HHMMSS
   ```

## Rollback

If something goes wrong:

1. **Restore previous database:**
   ```bash
   ssh -p 65002 u540587252@92.113.19.61
   cd /home/u540587252/domains/housekeepr.nl/public_html/database
   ls -la *.backup.*  # Find the backup file
   cp database.sqlite.backup.YYYYMMDD_HHMMSS database.sqlite
   ```

2. **Clear caches:**
   ```bash
   php artisan optimize:clear
   ```

## Support

- **Server:** Hostinger
- **SSH:** Port 65002
- **Domain:** housekeepr.nl
- **Email:** noreply@housekeepr.nl

