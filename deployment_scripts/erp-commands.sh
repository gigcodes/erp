#!/bin/bash

function HELP {
	echo "--command: custom commands"
}

args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
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

cd /var/www/erp.theluxuryunlimited.com

$command

if [ $? -ne 0 ]
then
	exit 1
fi
