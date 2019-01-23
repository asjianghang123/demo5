#!/bin/sh

PHP_PATH=/opt/lampp/bin/php

$PHP_PATH remainderData_lowAccess.php > /dev/null 2>&1
$PHP_PATH remainderData_highLost.php > /dev/null 2>&1
$PHP_PATH remainderData_badHandover.php > /dev/null 2>&1
