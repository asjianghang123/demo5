#!/bin/sh

PHP_PATH=/opt/lampp/bin/php

#清空txt
$PHP_PATH truncateTxt.php > /dev/null 2>&1

#清空表
mysql -uroot -pmongs <<EOF > /dev/null 2>&1
	use AutoKPI
	TRUNCATE TABLE temp_lowaccesscell;
	TRUNCATE TABLE temp_highlostcell;
	TRUNCATE TABLE temp_badhandovercell;
	TRUNCATE TABLE temp_neigh;
EOF
#echo -e "\nTRUNCATE TABLE temp_lowaccesscell/temp_highlostcell/temp_badhandovercell SUCCESS!"

#创建表/插入数据/生成对应城市小区基站
while read line
do
	$PHP_PATH lowAccessCell.php $line > /dev/null 2>&1
	$PHP_PATH highLostCell.php $line > /dev/null 2>&1
	$PHP_PATH badHandover.php $line > /dev/null 2>&1
	$PHP_PATH neigh.php $line > /dev/null 2>&1
done < "config/city.conf"
#echo -e "\nData Generation SUCCESS!"

#$PHP_PATH remainderData_lowAccess.php > /dev/null 2>&1

#stop Script
wait
#echo -e "\nAll is ok"
echo "OK"
exit 0
