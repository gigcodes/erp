while read line

set -eo pipefail
SCRIPT_NAME=`basename $0`

do
	echo "$line"|grep Processing
	if [ $? -eq 0 ]
	then
		scraper=`echo "$line"|cut -d' ' -f1`
		server=`echo "$line"|cut -d' ' -f2`
		year=`echo $line|cut -d' '  -f3|cut -d'-' -f1|tail -c 3`
		monthnum=`echo $line|cut -d' '  -f3|cut -d'-' -f2`
		day=`echo "$line"|cut -d' ' -f3|cut -d'-' -f3`
		month=`date +%b -d "$year-$monthnum-$day"`
		ssh -o ConnectTimeout=5 root@$server.theluxuryunlimited.com "ps -eo pid,etimes,args|grep $scraper|grep -v grep" < /dev/null
		if [ $? -ne 0 ]
		then
			endtime=`stat -c '%y' /mnt/logs/$server/$scraper-$day$month$year*.log|cut -d'.' -f1|tr ' ' '-'`
			sed -i "s/Processing-$scraper-$day-$server/$endtime/" /opt/scrap_history
		fi
	fi
done < /opt/scrap_history

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh ./monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
