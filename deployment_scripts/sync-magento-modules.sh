#!/bin/bash

function HELP {
        echo "-w|--website: website"
        echo "-s|--server: Server ip"
        echo "-d|--rootdir: rootdir"
	echo "-m|--modulename modulename"
        echo "-p|--path path"
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
                -h|--help)
                HELP
                exit 1
                ;;
                *)
                idx=$((idx+1))
                ;;
        esac
done

SSH_KEY="/opt/BKPSCRIPTS/id_rsa_websites"

function madd()
{
	ssh -i $SSH_KEY root@$server "cd $rootdir; composer require $modulename"

}

function sync()
{
#	echo "ssh -i $SSH_KEY root@$server \"cd $rootdir; bin/magento module:status\""
        input=`ssh -i $SSH_KEY root@$server "cd $rootdir; bin/magento module:status"`
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


$action
