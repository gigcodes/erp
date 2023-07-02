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


IS_EMAIL=`echo p | mail > /tmp/mail`
CHECKEMAIL=`echo $IS_EMAIL | grep -i "No mail for root"`
if [ -z $CHECKEMAIL ]
then
	echo "No incoming Emails"
	exit 0;
fi

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
		is_user=1
		MESSAGE=`bash /var/www/erp.theluxuryunlimited.com/deployment_scripts/webaccess-firewall.sh -f add -i $ip -c "$fromaddress-$comment"`
		if [ $? -eq 1 ]
		then
			STATUS=1
		else
			STATUS=0
		fi
		echo "Your Ip $ip has been whitelisted for erp access" 
	else
		is_user=0
	fi
	mysql -u $DB_USERNAME -h $DB_HOST -p$DB_PASSWORD erp_live -e "insert into ip_logs(email,ip,is_user,status,message,created_at,updated_at) values('$email','$ip','$is_user','$STATUS','$MESSAGE',now(),now())"
fi


