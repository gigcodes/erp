#!/bin/bash
###  This script is used to autoselect scraper server which has maximum memory available and start scraper there

command=$1
day=`date +%d`
for server in 0{1..9} {10..20}
do
	Used_mem=`ssh -i ~/.ssh/id_rsa -o ConnectTimeout=3 root@s$server.theluxuryunlimited.com 'free | grep Mem | awk '\''{print $3/$2 * 100.0}'\'''`
	if [ -z $Used_mem ]
	then
		Used_mem="100.00"
	fi
	echo $server $Used_mem >> /tmp/scrap_memory
done
scrapper=`cat /tmp/scrap_memory|sort -n -k2|head -n1|cut -d' ' -f1`

ssh root@s$scrapper.theluxuryunlimited.com "nohup node /root/scraper_nodejs/commands/completeScraps/$command.js &> /root/logs/$command-$day.log &"
rm /tmp/scrap_memory
echo $scrapper
