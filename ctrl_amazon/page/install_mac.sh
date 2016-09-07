#!/bin/bash
source ../../conf/page/conf_amazon.txt

if [ $# -ne 1 ]; then
    echo "Usage: $0 mac_idx!"
    exit 1
fi

mac_idx=$1


# Step 3: archive the files
archive_file=page.tar.gz
rm -f $archive_file
cd ../../plt
tar -cvzf ../ctrl_amazon/page/$archive_file page
cd -

# Step 4: upload & extract the archive
ip=${ip[$mac_idx]}
ssh_port=${ssh_port[$mac_idx]}
user=${user[$mac_idx]}
passwd=${passwd[$mac_idx]}
install_dir=${install_dir[$mac_idx]}

./run_remote.sh $ip $ssh_port $user $passwd "sudo mkdir -p $install_dir"
./run_remote.sh $ip $ssh_port $user $passwd "sudo chown -R ubuntu:www-data $install_dir"
./pcp.sh $ip $ssh_port $user $passwd $archive_file $install_dir
./run_remote.sh $ip $ssh_port $user $passwd "cd $install_dir -rf; sudo tar -xvzf $archive_file; rm $archive_file"
./run_remote.sh $ip $ssh_port $user $passwd "cd $install_dir -rf; sudo chown -R ubuntu:www-data page"
./run_remote.sh $ip $ssh_port $user $passwd "cd $install_dir -rf; find ./page -type d -print | xargs -i sudo chmod 775 {}"
./run_remote.sh $ip $ssh_port $user $passwd "cd $install_dir -rf; find ./page -type f -print | xargs -i sudo chmod 664 {}"

