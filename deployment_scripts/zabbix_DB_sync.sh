#!/bin/bash
subject=$1
message="DUMMY"
host=$2
etime=$4
odata=$7
severity=$5
eid=$8
dbuser="user"
dbpass="pass"
dbname="db"
dbhost="ip"


echo "eid $subject $message $host $etime $odata $severity $eid" &>> /tmp/hook.log

EVENTCHECK=`mysql -h $dbhost -u $dbuser -p$dbpass  -D $dbname -N -se "select event_id from zabbix_webhook_data where event_id = '$eid' order by id limit 1"`
if [ ! -z $EVENTCHECK ]
then
        IS_RESOLVED=`echo "$subject" | grep -i resolved | wc -l`
        echo "RES=======$IS_RESOLVED" &>> /tmp/hook.log
        if [ "$IS_RESOLVED" -gt "0" ]
        then
                echo "RES=======Marking resolved" &>> /tmp/hook.log
                mysql -h $dbhost -u $dbuser -p$dbpass  -D $dbname -N -se "update zabbix_webhook_data set zabbix_status_id = '1' where event_id = '$eid'"
                exit 0
        fi
fi

mysql -h $dbhost -u $dbuser -p$dbpass  -D $dbname -N -se "INSERT INTO zabbix_webhook_data (event_id,subject,message,event_start,host,severity,operational_data,created_at,updated_at) values ('$eid','$subject','$message','$etime','$host','$severity','$odata',now(),now())"
echo "INSERT INTO zabbix_webhook_data (event_id,subject,message,event_start,host,severity,operational_data,created_at,updated_at) values ('$eid','$subject','$message','$etime','$host','$severity','$odata',now(),now())"
