#!/bin/bash

SSHPORT="22480 2112 22"

for portssh in $SSHPORT
do
        ssh -p $portssh  -i ~/.ssh/id_rsa -q root@$SERVER 'exit'
        if [ $? -ne 255 ]
        then
                PORT=`echo $portssh`
        fi
done


function HELP {
        echo "-w|--website: website"
        echo "-s|--server: Server ip"
        echo "-d|--rootdir: rootdir"
	echo "-m|--modulename modulename"
        echo "-p|--path path"
	echo "-g|--project projectname(avoirchic,brands_labels,sololuxury,sololuxury,suvandnat,veralusso)"
	echo "-a|--action value(sync,add,enable,disable)"

}

args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
                -w|--website)
                website="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -s|--server)
                server="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -d|--rootdir)
                rootdir="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -p|--path)
                path="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
		-a|--action)
                action="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
		-m|--modulename)
                modulename="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
		-g|--project)
                project="${args[$((idx+1))]}"
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
MNAME="$modulename"
SSH_KEY="/opt/BKPSCRIPTS/id_rsa_websites"

function madd()
{
	ssh -p $PORT -i $SSH_KEY root@$server "cd $rootdir; composer require $modulename"

}

function sync()
{
#	echo "ssh -i $SSH_KEY root@$server \"cd $rootdir; bin/magento module:status\""
        input=`ssh -p $PORT -i $SSH_KEY root@$server "cd $rootdir; bin/magento module:status"`
# Initialize arrays for enabled and disabled modules
enabled_modules=()
disabled_modules=()

# Use a marker to determine whether we are processing enabled or disabled modules
marker=""

# Read the input line by line
while IFS= read -r line; do
  # Check for the marker and switch to "enabled" or "disabled" if found
  if [[ "$line" == "List of enabled modules:" ]]; then
    marker="enabled"
    continue
  elif [[ "$line" == "List of disabled modules:" ]]; then
    marker="disabled"
    continue
  fi

  # Append the module name to the appropriate array
  if [[ "$marker" == "enabled" ]]; then
    enabled_modules+=("$line")
  elif [[ "$marker" == "disabled" ]]; then
    disabled_modules+=("$line")
  fi
done <<< "$input"

# Print the separated modules
#echo "Enabled Modules:"
MARKER1="enabled="
for module in "${enabled_modules[@]}"; do
#  echo "$module"
  EMODULES="$EMODULES,$module"
done
	echo "enabled=$EMODULES"
echo 

#echo -e "\niiiDisabled Modules:"
for module in "${disabled_modules[@]}"; do
#  echo "$module"
  DMODULES="$DMODULES,$module"
done
echo "disabled=$DMODULES"
}


module_status()
{
	if [ "$action" == "enable" ]
	then
		EDF=1
	else
		EDF=0
	fi
	cd /opt/rawapps/
	git clone git@github.com:ludxb/brands-labels.git &> /dev/null
	cd brands-labels
	sed -i "s/.*'$MNAME'.*/\t'$MNAME' => $EDF,/" app/design/frontend/LuxuryUnlimited/$project/.deploy/config.php

	git add app/design/frontend/LuxuryUnlimited/$project/.deploy/config.php   &> /dev/null
	git commit -m 'Deployment config erp'  &> /dev/null
	git push origin stage  &> /dev/null
	if [ $? -eq 0 ]
	then
		echo "{\"status\":\"success\"}"
	else
		echo "{\"status\":\"fail\"}"
	fi

	cd /opt/rawapps/
	rm -rf brands-labels
}

#$action

case $action in

  add)
          echo $RCOUNT
    ;;

  enable)
          module_status
    ;;

  disable)
          module_status
    ;;
  sync)
          sync
    ;;

  *)
          echo "Failed"
    ;;
esac
