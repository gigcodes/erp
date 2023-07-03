#!/bin/bash


DB_USERNAME=erplive
DB_PASSWORD="Jb(hd4ersiuttG0iL"
DB_HOST=81.0.247.216

for i in "$@"
do
case $i in
    -s=*|--server=*)
    SERVER="${i#*=}"
    ;;
    -w=*|--searchpath=*)
    WEBSITE="${i#*=}"
    ;;
    -d=*|--lib=*)
    CWDDIR="${i#*=}"
    ;;
    --default)
    DEFAULT=YES
    ;;
    *)
            echo "Please Verify options"
    ;;
esac
done



#SERVER="212.90.120.84"
KEY="~/.ssh/id_rsa_all"
USER="root"
#CWDDIR="/home/prod-1-1/current/var/"
PORT="22"


ssh -i $KEY -p $PORT $USER@$SERVER "find $CWDDIR/var/ -type d -maxdepth 3 -exec chown -R www-data:www-data {} \;"
ssh -i $KEY -p $PORT $USER@$SERVER "find $CWDDIR/var/ -type d -maxdepth 3 -exec ls --time-style=long-iso -ldh {} \;" | awk -v SERVER="$SERVER" '{print SERVER "," $8 "," $3 "," $4 "," $1 "," $6 " " $7 "," $6 " " $7}' > /tmp/file_permissions.csv
mysqlimport -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD -f --local  --columns server,instance,owner,groupowner,permission,created_at,updated_at  --fields-terminated-by=, --lines-terminated-by="\n" erp_live /tmp/file_permissions.csv
