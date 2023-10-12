#!/bin/bash
set -eo pipefail
SCRIPT_NAME=`basename $0`

function HELP {
        echo "-r|--repo: Repo Name"
	echo "-s|--server: Live Server Ip"
}

args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
                -r|--repo)
                repo="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -s|--server)
                server="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -h|--help)
                HELP
                exit 1
                ;;
                *)
                idx=$((idx+1))
                ;;
        esac
done

ssh root@65.21.155.81 "rsync -az /home*/$repo/shared/pub/static/ root@$server:/home/$repo/shared/pub/static/"
echo "Assigning permissions"
ssh root@$server "chown -R www-data.www-data /home/$repo/shared/pub/static ; service varnish restart ; service php7.3-fpm restart ; redis-cli -n 0 FLUSHDB; redis-cli -n 1 FLUSHDB;"

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh ./monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
