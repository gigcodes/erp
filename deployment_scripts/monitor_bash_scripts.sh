#!/bin/bash

SCRIPT_NAME=`basename $0`

. /opt/etc/mysql-creds.conf

# Data to insert
SCRIPT_NAME=$1
SCRIPT_TYPE="cron"
OUTPUTFILE=$3
LAST_EXECUTION_STATUS=$2
OUTPUT=`cat $OUTPUTFILE | base64 -w 0`
# Define the SQL query to insert data
SQL_QUERY="INSERT INTO script_documents(file, script_type, comments, status, last_run) VALUES ('$SCRIPT_NAME', '$SCRIPT_TYPE', '$OUTPUT', '$LAST_EXECUTION_STATUS', now());"

# Execute the SQL query
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -h "$DB_HOST" -D "$DB_NAME" -e "$SQL_QUERY"
if [ $? -eq 0 ]; then
  echo "Data inserted successfully."
else
  echo "Error: Failed to insert data."
fi
rm -rf $OUTPUTFILE
