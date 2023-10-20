set -o pipefail
SCRIPT_NAME=`basename $0`

read -p "Please enter db name  " a 
mysqldump -h erpdb -u erplive -p  --no-data $a > $a.schema.sql | tee -a ${SCRIPT_NAME}.log

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh ./monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log


