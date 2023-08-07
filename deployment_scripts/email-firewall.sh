#!/bin/bash

##### Create a file with mysql credentials and place file path in MY_CREDS variable. 
## FILE FORMAT
#DB_USERNAME=username
#DB_PASSWORD=password
#DB_HOST=host or IP

## END file

MY_CREDS=/opt/etc/mysql-creds.conf
source $MY_CREDS
server=`echo $HOSTNAME`
#email_whitelisted=`mysql -u $DB_USERNAME -h $DB_HOST -p$DB_PASSWORD erp_live -N -se "select email from users where is_whitelisted='1'"`


IS_EMAIL=`echo p | mail > /tmp/mail`
CHECKEMAIL=`echo $IS_EMAIL | grep -i "No mail for root"`
if [ ! -z $CHECKEMAIL ]
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

email_whitelisted=`mysql -u $DB_USERNAME -h $DB_HOST -p$DB_PASSWORD erp_live -N -se "select email from users where is_whitelisted='1' and email='$email' "`

##### Check if sending mail account exist in our database whitelist ####
if [ ! -z "$email" ]
then
	cat /tmp/mail >> /opt/maillogs
#	echo $email_whitelisted|grep $email
#	if [ $? -eq 0 ]
	if [ ! -z "$email_whitelisted" ]
	then
		is_user=1
		echo "bash /var/www/erp.theluxuryunlimited.com/deployment_scripts/webaccess-firewall.sh -f add -i $ip -c \"$fromaddress-$comment\" -s $server"
		MESSAGE=`bash /var/www/erp.theluxuryunlimited.com/deployment_scripts/webaccess-firewall.sh -f add -i $ip -c "$fromaddress-$comment" -s $server`
		if [ $? -eq 1 ]
		then
			STATUS=1
			subject="IP whitelist failed"
			body="Ip $ip failed to add to $server <BR> $MESSAGE"
		else
			STATUS=0
			subject="IP whitelisting sucessfull"
			body="Ip $ip added to $server sucessfully<BR> $MESSAGE"
		fi
		echo "Your Ip $ip has been whitelisted for erp access" 
	else
		is_user=0
		subject="IP whitelist failed"
		body="Ip $ip is rejected $server <BR> $email is not registered or disabled"
	fi

	mysql -u $DB_USERNAME -h $DB_HOST -p$DB_PASSWORD erp_live -e "insert into ip_logs(server_name,email,ip,is_user,status,message,created_at,updated_at) values('$server','$email','$ip','$is_user','$STATUS','$MESSAGE',now(),now())"

generate_post_data()
{
  cat <<EOF
{
   "sender":{
      "name":"Mio-Moda",
      "email":"security@theluxuryunlimited.com"
   },
   "to":[
      {
         "email":"$email",
         "name":"$email"
      }
   ],
   "subject":"$subject",
   "htmlContent":"<html><head></head><body><p>Hello,</p>$body</p></body></html>"
}
EOF
}

echo $(generate_post_data)

curl --request POST --url https://api.brevo.com/v3/smtp/email --header 'accept: application/json' --header 'api-key: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' --header 'content-type: application/json' --data "$(generate_post_data)"

fi
