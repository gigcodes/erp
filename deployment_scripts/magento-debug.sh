#!/bin/bash

function HELP {
	echo "--server: Server Name"
	echo "--debug: true/false"
}

args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
                --server)
		server="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                --debug)
		debug="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                *)
                idx=$((idx+1))
                ;;
        esac
done
### Load environment variables
. /var/www/erp.theluxuryunlimited.com/.env

if [ $debug = "true" ]
then
	ssh -i ~/.ssh/id_rsa root@$server "cd /home/*/current/ ; bin/magento setup:config:set --enable-debug-logging=true ; bin/magento dev:query-log:enable ; bin/magento cache:flush"
else
	ssh -i ~/.ssh/id_rsa root@$server "cd /home/*/current/ ; bin/magento setup:config:set --enable-debug-logging=false ; bin/magento dev:query-log:disable ; bin/magento cache:flush ; rm -f var/debug/db.log"
fi
if [ $? -ne 0 ]
then
	exit 1
fi
