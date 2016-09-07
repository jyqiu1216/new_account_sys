#!/bin/bash
source ../../conf/account/conf_amazon.txt

if [ $# -ne 1 ]; then
    echo "Usage: $0 cmd!"
    exit 1
fi

cmd=$1
num=$svr_num

for ((idx=0;idx<num;++idx))
do
    ./run_cmd_mac.sh $idx "$cmd"
done

