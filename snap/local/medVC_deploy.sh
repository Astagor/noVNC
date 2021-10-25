#!/bin/bash
cd /snap/novnc/current
mkdir -p /var/www/html/novnc
cp *.php /var/www/html/novnc/
cp -r medvc_extra_services /var/www/html/novnc/
cp -r img /var/www/html/novnc/
cp -r app /var/www/html/novnc/
cp -r core /var/www/html/novnc/
cp -r vendor /var/www/html/novnc/
chown -R www-data:www-data /var/www/html/novnc
