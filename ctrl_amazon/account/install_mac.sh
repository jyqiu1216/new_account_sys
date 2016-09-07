#!/bin/bash
source ../../conf/account/conf_amazon.txt

if [ $# -ne 1 ]; then
    echo "Usage: $0 mac_idx!"
    exit 1
fi

mac_idx=$1

rm -r ../../plt/account/upload
rm -r ../../plt/account/mail
cp -r ../../tool/upload_log/upload ../../plt/account/upload
cp -r ../../tool/mail_tool/mail ../../plt/account/mail

# Step 3: archive the files
archive_file=account.tar.gz
rm -f $archive_file
cd ../../plt
tar -cvzf ../ctrl_amazon/account/$archive_file account
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
./run_remote.sh $ip $ssh_port $user $passwd "cd $install_dir -rf; tar -xvzf $archive_file; rm $archive_file"
./run_remote.sh $ip $ssh_port $user $passwd "cd $install_dir -rf; find ./account -type d -print  | sed '1d' | grep -v 'seaslog' | xargs -i sudo chown ubuntu:www-data -R {}"
./run_remote.sh $ip $ssh_port $user $passwd "cd $install_dir -rf; find ./account -type f -print  | grep -v 'seaslog' | xargs -i sudo chown ubuntu:www-data -R {}"
./run_remote.sh $ip $ssh_port $user $passwd "cd $install_dir -rf; sudo chown www-data:www-data -R ./account/seaslog"
./run_remote.sh $ip $ssh_port $user $passwd "cd $install_dir -rf; find ./account -type d -print | xargs -i sudo chmod 775 {}"
./run_remote.sh $ip $ssh_port $user $passwd "cd $install_dir -rf; find ./account -type f -print | grep -v '\.sh' | xargs -i sudo chmod 644 {}"
./run_remote.sh $ip $ssh_port $user $passwd "cd $install_dir -rf; find ./account -type f -print | grep '\.sh' | xargs -i sudo chmod 744 {}"


