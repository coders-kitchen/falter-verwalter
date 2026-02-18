# Heroku Deployment Guide

This guide covers deploying the Falter Verwalter app to Heroku.

## Prerequisites

1. **Heroku Account**: Sign up at https://www.heroku.com
2. **Heroku CLI**: Install from https://devcenter.heroku.com/articles/heroku-cli
3. **Git**: Initialize and commit your code to git

## Step 1: Prepare Your App

Ensure all changes are committed:

```bash
git status
git add .
git commit -m "Prepare for Heroku deployment"
```

## Step 2: Login to Heroku

```bash
heroku login
```

## Step 3: Create Heroku App

```bash
heroku create your-app-name
```

Replace `your-app-name` with your desired app name (must be unique).

## Step 4: Set Environment Variables

Your app needs critical environment variables. Set them on Heroku:

```bash
# Generate a new APP_KEY for production
php artisan key:generate --show

# Set the APP_KEY (copy from above command output)
heroku config:set APP_KEY="base64:YOUR_KEY_HERE"

# Other essential variables
heroku config:set APP_ENV=production
heroku config:set APP_DEBUG=false
heroku config:set LOG_CHANNEL=stack
```

## Step 5: Configure Database

### Option A: Use Heroku PostgreSQL (Recommended)

```bash
# Add PostgreSQL add-on
heroku addons:create heroku-postgresql:essential-0

# Verify the DATABASE_URL is set automatically
heroku config | grep DATABASE_URL
```

Then update your `.env` for production (when deploying, use the automatic `DATABASE_URL` from Heroku):

```env
DB_CONNECTION=pgsql
DB_HOST=from_database_url
DB_PORT=5432
DB_DATABASE=from_database_url
DB_USERNAME=from_database_url
DB_PASSWORD=from_database_url
```

Actually, Heroku automatically sets `DATABASE_URL`. Laravel automatically uses it if available.

### Option B: Use ClearDB MySQL

```bash
heroku addons:create cleardb:ignite

# Get the database URL
heroku config | grep CLEARDB_DATABASE_URL
```

## Step 6: Deploy to Heroku

```bash
git push heroku main
```

(or `master` if that's your branch name)

Monitor the deployment logs:

```bash
heroku logs --tail
```

## Step 7: Run Migrations

The `Procfile` automatically runs migrations on each deployment with:

```
release: php artisan migrate --force
```

To manually run migrations:

```bash
heroku run php artisan migrate --force
```

## Step 8: Create Admin User (Production)

Since UserSeeder is disabled in production, create an admin account manually:

```bash
heroku run php artisan tinker
```

Then in tinker:

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Your Admin Name',
    'email' => 'your-admin@example.com',
    'password' => Hash::make('STRONG_PASSWORD_HERE'),
    'role' => 'admin',
    'is_active' => true,
]);

exit;
```

## Step 9: Seed Reference Data (Optional)

Seed the database with reference data (regions, life forms, etc.):

```bash
heroku run php artisan db:seed --class=RegionSeeder
heroku run php artisan db:seed --class=LifeFormSeeder
heroku run php artisan db:seed --class=DistributionAreaSeeder
```

Or run all seeders (except test users):

```bash
heroku run php artisan db:seed
```

## Ongoing Operations

### View Logs

```bash
# Last 100 lines
heroku logs -n 100

# Tail (follow) logs
heroku logs --tail

# App logs only (exclude system logs)
heroku logs --source app
```

### Run Artisan Commands

```bash
heroku run php artisan COMMAND

# Examples:
heroku run php artisan tinker
heroku run php artisan cache:clear
heroku run php artisan config:clear
```

### Database Access

```bash
# Access database shell
heroku pg:psql  # For PostgreSQL
```

### Check Configuration

```bash
# View all config variables
heroku config

# Set a variable
heroku config:set VARIABLE_NAME=value

# Remove a variable
heroku config:unset VARIABLE_NAME
```

## Troubleshooting

### Build Fails

Check the logs:
```bash
heroku logs --tail
```

Common issues:
- Missing `APP_KEY`: Set it with `heroku config:set APP_KEY="base64:..."`
- Database connection: Verify `DATABASE_URL` is set
- Dependencies: Ensure `composer.lock` is committed

### App Crashes After Deploy

```bash
# Check recent error logs
heroku logs --tail

# Restart the app
heroku restart

# Restart specific dyno type
heroku restart worker
```

### Database Issues

```bash
# Reset database (WARNING: Deletes all data!)
heroku pg:reset DATABASE_URL --confirm your-app-name

# Run migrations after reset
heroku run php artisan migrate --force
```

### Clear Cache/Config

```bash
heroku run php artisan cache:clear
heroku run php artisan config:clear
heroku run php artisan view:clear
```

## Performance Tips

1. **Enable Caching**:
   ```bash
   heroku addons:create heroku-redis:premium-0
   heroku config:set CACHE_STORE=redis
   heroku run php artisan cache:clear
   ```

2. **Scale Dynos** (if needed):
   ```bash
   heroku ps:type Eco   # Eco dyno (free)
   heroku ps:type Basic # Basic dyno ($7/month)
   heroku ps:scale web=1 worker=1
   ```

3. **Monitor Resources**:
   ```bash
   heroku ps
   heroku logs --tail
   ```

## Security Checklist

Before going live:

- [ ] `APP_ENV=production` is set
- [ ] `APP_DEBUG=false` is set
- [ ] `APP_KEY` is set to a secure value
- [ ] Database is encrypted/secured
- [ ] Admin user created with strong password
- [ ] Test credentials removed from UI (already done)
- [ ] HTTPS is enforced (Heroku auto-provides free SSL)
- [ ] `.env` file is NOT committed to git
- [ ] Sensitive files in `.gitignore`

## Useful Commands Reference

```bash
# View app info
heroku apps:info

# Open app in browser
heroku open

# View all add-ons
heroku addons

# Scale up
heroku ps:scale web=2

# View release history
heroku releases

# Rollback to previous version
heroku rollback

# Delete app (careful!)
heroku apps:destroy --app your-app-name --confirm your-app-name
```

## Additional Resources

- [Heroku Node.js Support](https://devcenter.heroku.com/articles/nodejs-support)
- [Laravel on Heroku](https://devcenter.heroku.com/articles/getting-started-with-php)
- [Procfile Documentation](https://devcenter.heroku.com/articles/procfile)
- [Heroku Config Vars](https://devcenter.heroku.com/articles/config-vars)

---

**Need Help?**
- Check Heroku Status: https://status.heroku.com
- Heroku Support: https://help.heroku.com
- Laravel Documentation: https://laravel.com/docs
