#!/bin/bash

set -eo pipefail
SCRIPT_NAME=`basename $0`

function HELP {
	echo "--server: Server Name"
	echo "--type: which function to call"
	echo "--debug: true/false"
	echo "--test: test case"
	echo "--command: custom commands"
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
                --type)
		type="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                --debug)
		debug="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                --test)
		test="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                --command)
		command="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                *)
                idx=$((idx+1))
                ;;
        esac
done
### Load environment variables
. /var/www/erp.theluxuryunlimited.com/.env

if [ $type = "debug" ]
then
	if [ $debug = "true" ]
	then
		ssh -i ~/.ssh/id_rsa root@$server "cd /home/*/current/ ; bin/magento setup:config:set --enable-debug-logging=true ; bin/magento dev:query-log:enable ; bin/magento cache:flush"
	else
		ssh -i ~/.ssh/id_rsa root@$server "cd /home/*/current/ ; bin/magento setup:config:set --enable-debug-logging=false ; bin/magento dev:query-log:disable ; bin/magento cache:flush ; rm -f var/debug/db.log"
	fi
fi

if [ $type = "tests" ]
then
	ssh -i ~/.ssh/id_rsa root@$server "cd /home/*/current/ ; bin/magento dev:tests:run $test"
fi

if [ $type = "custom" ]
then
	ssh -i ~/.ssh/id_rsa root@$server "cd /home/*/current/ ; $command"
fi

#if [ $? -ne 0 ]
#then
#	exit 1
#fi

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh ./monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log

