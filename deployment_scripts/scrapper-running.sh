
SCRIPT_NAME=`basename $0`

for server in 0{1..9} {10..10}
do
	echo "#####################   Server -   s$server #################################" | tee -a ${SCRIPT_NAME}.log
	Total_mem=`ssh -i ~/.ssh/id_rsa root@s$server.theluxuryunlimited.com 'echo "scale=2; $(free -m|grep Mem|awk '\''{print $2}'\'')/1024" |bc'`
	Used_mem=`ssh -i ~/.ssh/id_rsa root@s$server.theluxuryunlimited.com 'echo "scale=2; $(free -m|grep Mem|awk '\''{print $3}'\'')/1024" |bc'`
	Used_mem_percentage=`echo "scale=2; $Used_mem/$Total_mem*100"|bc`
	echo "Total Memory = $Total_mem G" | tee -a ${SCRIPT_NAME}.log
	echo "Used Memory = $Used_mem G" | tee -a ${SCRIPT_NAME}.log
	echo "Used Memory in Percentage = $Used_mem_percentage%" | tee -a ${SCRIPT_NAME}.log
	ssh -i ~/.ssh/id_rsa root@s$server.theluxuryunlimited.com 'ps -eo pid,etime,args|grep command|grep -v grep|awk '\''{print $1 , $2 , $4}'\''' | tee -a ${SCRIPT_NAME}.log
done

if [[ $? -eq 0 ]]
then
   STATUS="Successful"
else
   STATUS="Failed"
fi

#Call monitor_bash_scripts

sh ./monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
