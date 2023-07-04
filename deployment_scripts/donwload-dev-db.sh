#!/bin/bash
DOWNLOAD_PATH="/storage/app/download_db/"

$user=
$pass=
for i in "$@"
do
case $i in
    -t=*|--prefix=*)
    type="${i#*=}"
    ;;
    -s=*|--searchpath=*)
    ip="${i#*=}"
    ;;
    -n=*|--lib=*)
    instance="${i#*=}"
    ;;
    -d=*|--lib=*)
    database="${i#*=}"
    ;;
    --default)
    DEFAULT=YES
    ;;
    *)
            echo "Please Verify options"
    ;;
esac
done

If [ “$instance” -eq “1” ]
then
	echo “instance 1”
else 
instance = “_2”
fi

mysqldump -u $user –p$pass --ignore-table=$database.sales_order $database > $DOWNLOAD_PATH/$database.sql
mysqldump -u $user –p$pass --no-data $database sales_order >> $DOWNLOAD_PATH/$database.sql
#ssh -n -p $SSH_PORT -i $SSH_KEY root@$ip "mysqldump -u $user $pass --ignore-table=$database$instance.sales_order $database$instance" >  $DOWNLOAD_PATH/$database.sql
#ssh -n -p $SSH_PORT -i $SSH_KEY root@$ip "mysqldump --no-data $database$instance sales_order" >>  $DOWNLOAD_PATH/$database$instance.sql


