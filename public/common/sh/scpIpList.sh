#!/usr/bin/expect -f

set remoteIp [lindex $argv 0]
set port [lindex $argv 1]
set user [lindex $argv 2]
set remoteFile [lindex $argv 3]
set localPath [lindex $argv 4] 
set fileName [lindex $argv 5]

# localFile=$1
# remoteIp=$2
# remoteFile=$3
# echo $remoteFile

#scp -P 220 /opt/lampp/htdocs/genius_zhujj/public/common/txt/siteKget/ipList_nantong_2018060103225824574.txt root@10.40.51.186:/home/zhujj
spawn su  mongs


expect "Password:"
send "Mongs@123\r"
expect "$"
send "rm -rf $localPath$fileName \r"
expect "$" 
send "scp -P $port $user@$remoteIp:$remoteFile $localPath \r"
expect "$"
send "chmod -R 777 $localPath$fileName \r"
expect "$"
send "exit\r"
interact