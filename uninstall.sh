#!/bin/sh

rm /conf/WGXepc
rm /conf/watchguard.sh
rm /conf/watchguard-settings.txt
rm /usr/local/www/widgets/widgets/watchguard.widget.php

sed -i '' '/watchguard/d' /etc/crontab

service cron restart

echo "Uninstalled !"