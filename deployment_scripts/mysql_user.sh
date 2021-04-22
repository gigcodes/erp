#!/bin/bash

mysql_db=erp_live
function Create {
	check_user=`mysql -e "select user from mysql.user where user='$mysql_user'"`
	if [ -z "$check_user" ]
	then
		mysql <<QUERY
		CREATE USER '$mysql_user'@'localhost' IDENTIFIED BY '$mysql_pass';
		FLUSH PRIVILEGES;
QUERY
	else
		echo "User already created"
	fi
}

function Delete {
	check_user=`mysql -e "select user from mysql.user where user='$mysql_user'"`
	if [ -z "$check_user" ]
	then
		echo " User Does not exist"
	else
		mysql <<QUERY
		Delete from mysql.user where user='$mysql_user';
		FLUSH PRIVILEGES;
QUERY
	fi
}

function Update {
	check_user=`mysql -e "select user from mysql.user where user='$mysql_user'"`
	if [ -z "$check_user" ]
	then
		echo " User Does not exist"
	else
		if [ -z "$mysql_table" ]
		then
			echo "Please enter table name to give permission"
		else
			if [ "$permission_type" = "read" ]
			then
				type="select"
			else
				type="select,insert,update"
			fi

			for table_name in $(echo $mysql_table | sed "s/,/ /g")
			do
				mysql <<QUERY
				GRANT $type ON $mysql_db.$table_name TO '$mysql_user'@'localhost';
				FLUSH PRIVILEGES;
QUERY
			done
		fi
	fi
}

function Revoke {
	check_user=`mysql -e "select user from mysql.user where user='$mysql_user'"`
	if [ -z "$check_user" ]
	then
		echo " User not exist"
	else
		if [ -z "$mysql_table" ]
		then
			echo "Please enter table name to revoke permission"
		else
			for table_name in $(echo $mysql_table | sed "s/,/ /g")
			do
				mysql <<QUERY
				REVOKE select,insert,update ON $mysql_db.$table_name from '$mysql_user'@'localhost';
				FLUSH PRIVILEGES;
QUERY
			done
		fi
	fi
}

function HELP {
	echo " -f: Function (create - create new mysql user with given password)
		(delete - Delete mysql user)
		(update - Assign insert & update permission on specific table)
		(revoke - Revoke insert & update permission from all tables)"
	echo " -u: Mysql User"
	echo " -m: Permission type read/write to specific table"
	echo " -t: Mysql Database Table"
	echo " -p: Mysql user Password"
}

while getopts ":f:u:t:p:m:h" opt; do
	case $opt in
		f)
			function=$OPTARG
			;;
		u)
			mysql_user=$OPTARG
			;;
		t)
			mysql_table=$OPTARG
			;;
		p)
			mysql_pass=$OPTARG
			;;
		m)
			permission_type=$OPTARG
			;;
		h)
			HELP
			;;
		:)
			HELP
			;;
	esac
done

if [ "$function" = "create" ]
then
	Create
elif [ "$function" = "delete" ]
then
	Delete
elif [ "$function" = "update" ]
then
	Update
elif [ "$function" = "revoke" ]
then
	Revoke
fi
