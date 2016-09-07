#!/bin/bash

source ../../project_conf/project.conf

if [ 0 -eq $environment ]
then
    source ../../conf/account/conf.txt
else
    source ../../conf/account/conf_amazon.txt
fi

cd ../../ctrl_amazon/account

cur_apptype="account_sys"


cur_logtype="account"
for((i=0;i<${svr_num};i++))
do
    ./run_cmd_mac.sh ${i} "cd ./account/upload; ./upload.sh $cur_apptype $cur_logtype &> /dev/null";
done



