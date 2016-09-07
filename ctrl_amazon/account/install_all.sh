#!/bin/bash
source ../../conf/account/conf_amazon.txt

num=$svr_num

echo "It's going to install pro_sys to $num machines!"
echo "Are you sure to continue? (Press Y to confirm)"
read flag
if [ $flag != "y" -a $flag != "Y" ]; then
    exit 1
fi

for ((idx=0;idx<num;++idx))
do
    ./install_mac.sh $idx
done


