#!/bin/bash

if [ $# -eq 3 ]
then
	cur_apptype=$1
	cur_logtype=$2
	upload_hour=$3
elif [ $# -eq 2 ]
then
	cur_apptype=$1
	cur_logtype=$2
	upload_hour=1
else
	echo "$0 proj_name module_name upload_hour"
	exit 0
fi

cur_date=`date -d"${upload_hour} hours ago" +%Y%m%d`
cur_year=`date -d"${upload_hour} hours ago" +%Y`
cur_month=`date -d"${upload_hour} hours ago" +%m`
cur_day=`date -d"${upload_hour} hours ago" +%d`
cur_hour=`date -d"${upload_hour} hours ago" +%H`
cur_ip=`/sbin/ifconfig | grep "inet addr" | head -n1 | awk -F':' '{print $2}' | awk '{print $1}'`

# save module log
if [ -e "../seaslog/${cur_date}${cur_hour}.log" ]
then
        ip=ec2-54-205-114-175.compute-1.amazonaws.com
        ssh_port=22
        user=ubuntu
        passwd=ubuntu
        install_dir=/data1/${cur_apptype}_serv_log/${cur_logtype}/${cur_date}

		echo "../seaslog/${cur_date}${cur_hour}.log"
		echo "${install_dir}/${cur_date}${cur_hour}_${cur_ip}.log"
		
        ./run_remote.sh $ip $ssh_port $user $passwd "sudo mkdir -p $install_dir"
        scp -i /home/ubuntu/stupid.pem ../seaslog/${cur_date}${cur_hour}.log ${user}@${ip}:${install_dir}/${cur_date}${cur_hour}_${cur_ip}.log
fi

#del history log
((del_hour=${upload_hour}+24))
del_day=`date -d"${del_hour} hours ago" +%Y%m%d`
if [ ${cur_date} \> ${del_day} ]
then
	echo "../seaslog/${del_day}${cur_hour}.log"
	sudo rm ../seaslog/${del_day}${cur_hour}.log
fi

find ../seaslog -mtime +30 | xargs -i sudo rm {}
