#!/bin/bash

if [ $# != 1 ]; then
echo "usage:stopMonitor [taskName]"
exit 0
fi

ps -ef | grep $1 | awk '{print $2}'| while read pid
do
	sudo kill -9 $pid
done
echo "success"
