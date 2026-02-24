web: vendor/bin/heroku-php-apache2 public/
release: npm run build && php artisan migrate --force && php artisan db:seed --class=ChangelogEntrySeeder --force
