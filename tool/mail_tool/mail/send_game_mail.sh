#!/bin/bash

cd ../game_mail/gt
ls | grep -v ".log" | grep -v "game_mail.json" | while read mail_file
do
	mv ${mail_file} ${mail_file}.sh
	sudo chmod +x ${mail_file}.sh
	./${mail_file}.sh
	mv ${mail_file}.sh ${mail_file}.log
	sudo chmod 644 ${mail_file}.log
done 
cd -
