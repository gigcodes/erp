set -eo pipefail
SCRIPT_NAME=`basename $0`

server=$1
pid=$2
ssh -i ~/.ssh/id_rsa root@$server.theluxuryunlimited.com "kill -9 $pid" | tee -a ${SCRIPT_NAME}.log

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh ./monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
