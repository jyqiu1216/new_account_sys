#!/bin/bash
source ../../conf/modules/conf.txt

if [ $# -ne 2 ]; then
    echo "Usage: $0 <local file/dir> <remote relative file/dir>!"
    exit 1
fi

num=$svr_num

for ((idx=0;idx<num;++idx))
do
    ./upload_mac.sh $idx $1 $2
done


