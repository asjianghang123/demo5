#!/bin/sh
cd /opt/common/ltetools
./ltng-decoder -f $1 -t ./L14B/ > $2
gzip $2
