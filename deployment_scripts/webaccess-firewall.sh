#!/bin/bash

function Add {
	ufw insert 1 allow proto tcp from $IP to any port '80,443'
}

function List {
	ufw status numbered|tr '][' ' '|grep 80,443|awk '{print $1,$5}'
}

function Delete {
	ufw status numbered|tr '][' ' '|grep 80,443|awk '{print $1}' |grep -w "$IP_Numbered"
	if [ $? -eq 0 ]
	then
		yes|ufw delete $IP_Numbered
	else
		echo "Number is not in the list"
		exit 1
	fi
}

function HELP {
	echo " -f: Function (add - Add New Ip for web access)
		(delete - Delete Ip for web access)
		(list - List of ips who have access to erp)"
	echo "-n|--number: Number in list of ips which need to delete for web access"
	echo "-i|--ip: Ip address to add in whitelist for erp access"
}

args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
	        -f|--function)
	        function="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -n|--number)
	        IP_Numbered="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -i|--ip)
	        IP="${args[$((idx+1))]}"
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

if [ "$function" = "add" ]
then
	Add
elif [ "$function" = "delete" ]
then
	Delete
elif [ "$function" = "list" ]
then
	List
fi
