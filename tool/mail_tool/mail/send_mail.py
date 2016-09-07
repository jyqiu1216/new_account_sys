#!/usr/bin/python
#-*- encoding: utf-8 -*-

import boto
from boto import ses
import sys

boto.set_file_logger('boto', './boto.log', 10)

def SendMail(receiver, mail_file):
    conn = boto.ses.connect_to_region('us-east-1',aws_access_key_id='AKIAIVZGAYRGJCZQQLOQ',aws_secret_access_key='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')
    all_the_text = open(mail_file).read()
    conn.send_raw_email(all_the_text)

if __name__ == '__main__':
    MailFile = sys.argv[1]
    Receiver = []
    for i in range(len(sys.argv) - 2):
        Receiver.insert(-1,sys.argv[2 + i])
    SendMail(Receiver, MailFile)
    

