import sys
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

#tolist = ('terry@leyinetwork.com', 'daemon@leyinetwork.com', 'nemo@leyinetwork.com', 'rock@leyinetwork.com', 'snow@leyinetwork.com', 'wave@leyinetwork.com')
tolist = ('snow@leyinetwork.com')
sender = 'terry@leyinetwork.com'
passwd = 'leyi2014'
mailserver = 'smtp.exmail.qq.com'

class EmailSender:
    def __init__(self, mail_user, mail_pass, mail_server, mail_port=465):
        self.mail_user = mail_user
        self.mail_pass = mail_pass
        self.mail_server = mail_server
        self.mail_port = mail_port
			
    def sendReportEmail(self, content, mail_to = tolist):
        msg = MIMEMultipart('alternatvie')
        msg['Subject'] = 'account_sys_warnning'
        msg['From'] = self.mail_user
        msg['To'] = ','.join(mail_to)
        report = MIMEText(content, _subtype='plain', _charset='UTF-8')
        msg.attach(report)
        try:
            self.conn = smtplib.SMTP_SSL(self.mail_server, self.mail_port)
            self.conn.login(self.mail_user, self.mail_pass)
            print msg.as_string()
            self.conn.sendmail(self.mail_user, mail_to, msg.as_string())
            self.conn.quit()
        except Exception,e:
            print str(e)

mail_sender = EmailSender(sender, passwd, mailserver)

if __name__ == '__main__':
    mail_sender.sendReportEmail(sys.argv[1])
