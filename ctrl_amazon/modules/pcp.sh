#!/usr/bin/expect --



if { [llength $argv] < 6} { 

	puts "usage: $argv0 ip port user passwd filename destpath"  
	exit 1
}
set success 0

set maxRetry 1
for {set retryNum 0} {$retryNum<$maxRetry} {incr retryNum} {

spawn  /usr/bin/scp -r -i /home/ubuntu/stupid.pem [lindex $argv 4] [lindex $argv 2]@[lindex $argv 0]:[lindex $argv 5]

set timeout 600
expect { 
	
	"password:" {    	
		send "[lindex $argv 3]\r"
		expect eof
		set success 1
		break
	}

	"yes/no)?" {
		send "yes\r"
		expect "password:" {
			send "[lindex $argv 3]\r"
			expect eof
			set success 1
			break
		}
	}
	timeout {continue}
	eof {continue}
}
}

if { $success==0 } {
 exit 0
}

