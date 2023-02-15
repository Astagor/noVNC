#!/bin/bash
cd /var/www/noVNC
mkdir -p /var/www/html/medvc_remote
cp index.php /var/www/html/medvc_remote/
cp -r img /var/www/html/medvc_remote/
cp -r app /var/www/html/medvc_remote/
cp -r core /var/www/html/medvc_remote/
cp -r vendor /var/www/html/medvc_remote/
chown -R www-data:www-data /var/www/html/medvc_remote
cp services/novnc.service /etc/systemd/system/novnc.service
systemctl daemon-reload
systemctl enable novnc.service
systemctl restart novnc.service
