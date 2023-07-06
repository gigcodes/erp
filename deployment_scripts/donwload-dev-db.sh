#!/bin/bash
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
	echo "instance 1"
else 
db_instance = "_2"
fi

for port in $possible_ssh_port
do
	echo "check port = $port"
	telnet_output=`echo quit | telnet $ip $port 2>/dev/null | grep Connected`
	if [ ! -z "$telnet_output" ]
	then
		SSH_PORT=$port
	fi
done

ssh -n -p $SSH_PORT -i $SSH_KEY root@$ip "mysqldump --ignore-table=$database$db_instance.sales_order $database$db_instance" >  /tmp/$database$db_instance.sql
echo "port number is = $SSH_PORT"
ssh -n -p $SSH_PORT -i $SSH_KEY root@$ip "mysqldump --no-data $database$db_instance sales_order >>  /tmp/$database$db_instance.sql"

if [ "$?" -eq "0" ]
then
	scp -P $SSH_PORT -i $SSH_KEY root@$ip:/tmp/$database$db_instance.sql $DOWNLOAD_PATH/$database.sql
fi



