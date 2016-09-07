#!/bin/bash

source ../../project_conf/project.conf

if [ 0 -eq $environment ]
then
    source ../../conf/account/conf.txt
else
    source ../../conf/account/conf_amazon.txt
fi

cd ../../ctrl_amazon/account

for((i=0;i<${svr_num};i++))
do
    ./run_cmd_mac.sh ${i} "cd ./account/mail; ./send_account_mail.sh  &> /dev/null";
    ./run_cmd_mac.sh ${i} "cd ./account/mail; ./send_game_mail.sh  &> /dev/null";
done

