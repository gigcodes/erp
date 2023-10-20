#!/bin/bash
dbuser="emailsync"
dbpass="emailsync"
dbname="erp_live"
dbhost="81.0.247.216"


#mysql -h $dbhost -u $dbuser -p$dbpass -D $dbname -N -se "select username, password from email_addresses"

MAIL_PHP="/opt/pipe-php-email-reader/mailsync.php"
EMAILFILE="/opt/pipe-php-email-reader/email.list"
while read mail;
do

        emailid=`echo $mail | awk '{print $1}'`
        password=`echo $mail | awk '{print $2}'`
        echo "Read EMAIL for mailbox $emailid $password"
        php7.4 $MAIL_PHP $emailid $password &>> /opt/pipe-php-email-reader/sync.log
done<$EMAILFILE
