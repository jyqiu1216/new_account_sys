#!/bin/bash
source ../../conf/page/conf_amazon.txt

if [ $# -ne 2 ]; then
    echo "Usage: $0 mac_idx cmd!"
    exit 1
fi

mac_idx=$1
cmd=$2

ip=${ip[$mac_idx]}
ssh_port=${ssh_port[$mac_idx]}
user=${user[$mac_idx]}
passwd=${passwd[$mac_idx]}
install_dir=${install_dir[$mac_idx]}

./run_remote.sh $ip $ssh_port $user $passwd "cd $install_dir; $cmd"

