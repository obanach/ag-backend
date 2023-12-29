cp /home/site/wwwroot/azure/nginx /etc/nginx/sites-available/default
service nginx restart
php bin/console lexik:jwt:generate-keypair --skip-if-exists