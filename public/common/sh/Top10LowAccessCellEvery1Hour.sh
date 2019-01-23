#!/bin/bash
ssh root@Slave0 > /dev/null 2>&1 << eeooff
cd /opt/mongs/mongodb_gshark_task && python mongodb_gshark_task_single_file.py -d ctr -c $1 -t $2 -n $3
exit
eeooff
#echo done!