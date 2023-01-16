email_whitelisted=`mysql -u erplive -h erpdb -pC*jlP2E0nbj6 erp_live -e "select email from users where is_whitelisted='1'"`

echo p | mail > /tmp/mail
ip=`grep 'Subject: ' /tmp/mail|cut -d' ' -f2|cut -d'-' -f2`
comment=`grep 'Subject: ' /tmp/mail|cut -d' ' -f2|cut -d'-' -f1`
fromaddress=`grep 'From: ' /tmp/mail|cut -d' ' -f2`
email=`grep 'From: ' /tmp/mail|cut -d'<' -f2|cut -d'>' -f1`

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
