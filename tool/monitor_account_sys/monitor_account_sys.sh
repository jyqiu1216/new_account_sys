#!/bin/bash

num=`ps -ef | grep "monitor_account_sys.sh" | grep -v grep | wc -l`
if ((num>3))
then
        echo "monitor_account_sys.sh exist[$num]!"
        exit
fi

php5 monitor_account_sys.php 

if [ -f warnning_file ]; then
    cat warnning_file | xargs -i python send_warnning_mail.py {}
    rm warnning_file
fi


