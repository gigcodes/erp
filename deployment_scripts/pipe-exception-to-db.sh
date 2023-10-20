#!/bin/bash
SSH_KEY="/opt/BKPSCRIPTS/id_rsa_websites"
while read line
do 
	SSH_PORT=`echo $line | awk '{print $4}'`
	IP=`echo $line | awk '{print $1}'`
	BKP_SRC=`echo $line | awk '{print $2}'`
	BKP_DST=`echo $line | awk '{print $3}'`
	echo $line

	function logparse()
	{

	for path in $(ssh -n -p $SSH_PORT -i $SSH_KEY root@$IP ls $BKP_SRC/current/var/log/*.log | grep current)
	do
		echo $path
		ERROR=`ssh -n -p $SSH_PORT -i $SSH_KEY root@$IP retail $path | grep -v mixins.min.js | grep -i -A 4 -B 1 "Exception"`
		TYPE="$(basename $path .log)"
		echo $TYPE

		function quoteSQL() {
		    printf "FROM_BASE64('%s')" "$(echo -n "$ERROR" | base64 -w0 )"
		}

		echo "$ERROR"
		WEB_ID=`echo $line | awk '{print $5}'`
		if [ ! -z "$ERROR" ]
		then

			mysql -h 81.0.247.216 -u erplive -p'Jb(hd4ersiuttG0iL' -e "insert into erp_live.website_logs (sql_query,module,website_id,error,type,file_path,created_at,updated_at,time) values('Stack Trace','NA','$WEB_ID',$(quoteSQL "$PASSWORD"),'$TYPE','$path',now(),now(),'0.0000');"
		fi

	done
	}
	logparse &
done </opt/BKPSCRIPTS/server_ip.list
