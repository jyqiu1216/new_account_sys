#!/bin/bash
source ../../conf/modules/conf_amazon.txt

if [ $# -ne 3 ]; then
    echo "Usage: $0 mac_idx <local file/dir> <remote relative file/dir>!"
    exit 1
fi

mac_idx=$1

ip=${ip[$mac_idx]}
ssh_port=${ssh_port[$mac_idx]}
user=${user[$mac_idx]}
passwd=${passwd[$mac_idx]}
install_dir=${install_dir[$mac_idx]}
local_file=$2
dest_path=$install_dir/$3

./pcp.sh $ip $ssh_port $user $passwd $local_file $dest_path


