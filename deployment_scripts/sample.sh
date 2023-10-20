#!/bin/bash
ERROR=$(cat /mnt/logs/website/suvandnat/prod-1-1/sample.log  | grep -i -A 4 -B 1 "Exception")
echo "$ERROR"
mysql -h 81.0.247.216 -u erplive -p'Jb(hd4ersiuttG0iL' -e "insert into erp_live.website_logs (website_id,error,type,file_path,created_at,updated_at) values('sololuxury','$ERROR','test','/mnt/',now(),now())"
