#!/bin/bash

function HELP {
        echo "-s|--server	Server ip"
        echo "-d|--rootdir	rootdir"
        echo "-c|--command	Command to execute"
        echo "-h|--help"
}


args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
                -s|--server)
                server="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -d|--rootdir)
                rootdir="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -c|--command)
                command="${args[$((idx+1))]}"
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


if [ -z "$server" ] || [ -z "$rootdir" ]; then
    # Both variables are null or missing, so execute your help function
    echo "Please check for missing variables"
    HELP
    exit 1
fi


SSH_KEY="/opt/BKPSCRIPTS/id_rsa_websites"
SSHPORT="22480 2112 22"

for portssh in $SSHPORT
do
        ssh -p $portssh  -i $SSH_KEY -q root@$server 'exit'
        if [ $? -ne 255 ]
        then
                PORT=`echo $portssh`
        fi
done

if [ -z "$rootdir" ]
then
#	echo "ssh -p $PORT -i $SSH_KEY root@$server \"$command\""
	ssh -p $PORT -i $SSH_KEY root@$server "base64 -d <<< $command | sh"

else
#	echo "ssh -p $PORT -i $SSH_KEY root@$server \"cd $rootdir; $command\""
	ssh -p $PORT -i $SSH_KEY root@$server "cd $rootdir; base64 -d <<< $command | sh"

fi