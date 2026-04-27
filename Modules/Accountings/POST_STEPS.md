# Post Steps (absolute paths)

cd /home/saassmar/domains/admin.cleanhub.pro/public_html && php artisan optimize:clear
cd /home/saassmar/domains/admin.cleanhub.pro/public_html && php artisan module:cache-clear || true
cd /home/saassmar/domains/admin.cleanhub.pro/public_html && php artisan module:migrate Accountings
