#!/bin/bash
set -eo pipefail
SCRIPT_NAME=`basename $0`

DOWNLOAD_PATH="/var/www/erp.theluxuryunlimited.com/storage/app/download_db"

MY_CREDS=/opt/etc/mysql-creds.conf
source $MY_CREDS
SSH_KEY="/opt/BKPSCRIPTS/id_rsa_websites"
args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
                -t|--type)
                type="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -s|--server)
                ip="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -n|--instance)
                instance="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -d|--database)
                database="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -h|--help)
                HELP
                exit 1
                ;;
                *)
                	echo "Please verify options"
                ;;
        esac
done

if [ "$instance" -eq "1" ]
then
	echo "instance 1" | tee -a ${SCRIPT_NAME}.log
else 
db_instance = "_2" | tee -a ${SCRIPT_NAME}.log
fi

for port in $possible_ssh_port
do
	echo "check port = $port"
	telnet_output=`echo quit | telnet $ip $port 2>/dev/null | grep Connected`
	if [ ! -z "$telnet_output" ]
	then
		SSH_PORT=$port | tee -a ${SCRIPT_NAME}.log
	fi
done

ssh -n -p $SSH_PORT -i $SSH_KEY root@$ip "mysqldump --ignore-table=$database.sales_order $database" >  /tmp/$database.sql | tee -a ${SCRIPT_NAME}.log
echo "port number is = $SSH_PORT" | tee -a ${SCRIPT_NAME}.log | tee -a ${SCRIPT_NAME}.log
ssh -n -p $SSH_PORT -i $SSH_KEY root@$ip "mysqldump --no-data $database sales_order >>  /tmp/$database.sql" | tee -a ${SCRIPT_NAME}.log

if [ "$?" -eq "0" ]
then
	scp -P $SSH_PORT -i $SSH_KEY root@$ip:/tmp/$database$db_instance.sql $DOWNLOAD_PATH/$database.sql | tee -a ${SCRIPT_NAME}.log
fi



if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh ./monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
