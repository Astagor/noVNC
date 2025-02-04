#!/bin/bash
cd /var/www/noVNC
mkdir -p /var/www/html/novnc
cp *.php /var/www/html/novnc/
cp -r medvc_extra_services /var/www/html/novnc/
cp -r img /var/www/html/novnc/
cp -r app /var/www/html/novnc/
cp -r core /var/www/html/novnc/
cp -r vendor /var/www/html/novnc/
chown -R www-data:www-data /var/www/html/novnc
chown -R www-data:www-data /var/www/noVNC
chmod +x utils/novnc_proxy
chmod o+rx /etc/letsencrypt/live
chmod o+rx /etc/letsencrypt/archive
cp services/novnc.conf.medvc.eu.service /etc/systemd/system/novnc.conf.medvc.eu.service
cp services/novnc2.conf.medvc.eu.service /etc/systemd/system/novnc2.conf.medvc.eu.service
cp services/novnc3.conf.medvc.eu.service /etc/systemd/system/novnc3.conf.medvc.eu.service
cp services/novnc.speakers.tnc.geant.org.service /etc/systemd/system/novnc.speakers.tnc.geant.org.service
systemctl daemon-reload
systemctl enable novnc.conf.medvc.eu.service
systemctl enable novnc2.conf.medvc.eu.service
systemctl enable novnc3.conf.medvc.eu.service
systemctl enable novnc.speakers.tnc.geant.org.service
systemctl restart novnc.conf.medvc.eu.service
systemctl restart novnc2.conf.medvc.eu.service
systemctl restart novnc3.conf.medvc.eu.service
systemctl restart novnc.speakers.tnc.geant.org.service
