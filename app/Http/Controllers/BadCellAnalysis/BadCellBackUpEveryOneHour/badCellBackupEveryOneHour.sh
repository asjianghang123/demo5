#!/bin/sh

PHP_PATH=/opt/lampp/bin/php

badcell_path=/opt/lampp/htdocs/genius/app/Http/Controllers/BadCellAnalysis/BadCellBackUpEveryOneHour

ps -ef | grep start_back.php | grep -v grep | awk '{print $2}'|xargs kill -9

if [ `ps -ef | grep start_back.php | wc -l | awk '{print $1}'` -le 1 ]
then
	cd $badcell_path
	$PHP_PATH start_back.php &
fi
