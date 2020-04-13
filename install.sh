#!/bin/sh

curl https://raw.githubusercontent.com/H4ckd4ddy/pfSense-Watchguard-widget/master/src/WGXepc --output /conf/WGXepc
chmod +x /conf/WGXepc
curl https://raw.githubusercontent.com/H4ckd4ddy/pfSense-Watchguard-widget/master/src/watchguard.sh --output /conf/watchguard.sh
chmod +x /conf/watchguard.sh
curl https://raw.githubusercontent.com/H4ckd4ddy/pfSense-Watchguard-widget/master/src/watchguard-settings.txt --output /conf/watchguard-settings.txt

curl https://raw.githubusercontent.com/H4ckd4ddy/pfSense-Watchguard-widget/master/src/watchguard.widget.php --output /usr/local/www/widgets/widgets/watchguard.widget.php

echo "* * * * * root /conf/watchguard.sh" >> /etc/crontab
echo "* * * * * root sleep 15;/conf/watchguard.sh" >> /etc/crontab
echo "* * * * * root sleep 30;/conf/watchguard.sh" >> /etc/crontab
echo "* * * * * root sleep 45;/conf/watchguard.sh" >> /etc/crontab
echo "" >> /etc/crontab

service cron restart

echo "Installed !"