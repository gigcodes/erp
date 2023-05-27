#!/bin/bash

##### Create a file with mysql credentials and place file path in MY_CREDS variable. 
## FILE FORMAT
#DB_USERNAME=username
#DB_PASSWORD=password
#DB_HOST=host or IP

## END file

MY_CREDS=/opt/etc/mysql-creds.conf
source $MY_CREDS

email_whitelisted=`mysql -u $DB_USERNAME -h $DB_HOST -p$DB_PASSWORD erp_live -e "select email from users where is_whitelisted='1'"`

echo p | mail > /tmp/mail
ip=`grep 'Subject: ' /tmp/mail|cut -d' ' -f2|cut -d'-' -f2`
comment=`grep 'Subject: ' /tmp/mail|cut -d' ' -f2|cut -d'-' -f1`
fromaddress=`grep 'From: ' /tmp/mail|cut -d' ' -f2`
email=`grep 'From: ' /tmp/mail|cut -d'<' -f2|cut -d'>' -f1`

echo "Checking for new emails.........."
echo "Email: $email"
echo "IP Address : $ip"

##### Check if sending mail account exist in our database whitelist ####
if [ ! -z $email ]
then
	cat /tmp/mail >> /opt/maillogs
	echo $email_whitelisted|grep $email
	if [ $? -eq 0 ]
	then
		bash /var/www/erp.theluxuryunlimited.com/deployment_scripts/webaccess-firewall.sh -f add -i $ip -c "$fromaddress-$comment"
		echo "Your Ip $ip has been whitelisted for erp access" 
	fi
fi
