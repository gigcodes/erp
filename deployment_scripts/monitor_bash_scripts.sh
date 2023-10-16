#!/bin/bash
set -eo pipefail
SCRIPT_NAME=`basename $0`

source /opt/etc/mysql-creds.conf

# Data to insert
SCRIPT_NAME=$1
SCRIPT_TYPE="cron"
OUTPUT=$3
LAST_EXECUTION_STATUS=$2

# Define the SQL query to insert data
SQL_QUERY="INSERT INTO monitor_scripts (file, script_type, output, status, last_run) VALUES ('$SCRIPT_NAME', '$SCRIPT_TYPE', '$OUTPUT', '$LAST_EXECUTION_STATUS', now());"

# Execute the SQL query
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -h "$DB_HOST" -D "$DB_NAME" -e "$SQL_QUERY" | tee -a ${SCRIPT_NAME}.log

# Check the exit status to see if the query was successful

if [ $? -eq 0 ]; then
  echo "Data inserted successfully."
else
  echo "Error: Failed to insert data."
fi

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh ./monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
