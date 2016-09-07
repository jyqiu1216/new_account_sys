#!/bin/bash

ls ../register_mail | grep -v ".log" | while read mail_file
do
    python send_mail.py ../register_mail/${mail_file}
    mv ../register_mail/${mail_file} ../register_mail/${mail_file}.log
done


ls ../active_mail | grep -v ".log" | while read mail_file
do
	python send_mail.py ../active_mail/${mail_file}
	mv ../active_mail/${mail_file} ../active_mail/${mail_file}.log
done 


ls ../reset_passwd | grep -v ".log" | while read mail_file
do
	python send_mail.py ../reset_passwd/${mail_file} 
	mv ../reset_passwd/${mail_file} ../reset_passwd/${mail_file}.log
done 

