read -p "Please enter db name  " a 
mysqldump -h erpdb -u erplive -p  --no-data $a > $a.schema.sql

