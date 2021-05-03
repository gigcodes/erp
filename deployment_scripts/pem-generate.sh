########### Input Argument for Server Name which need to regenrate pem file #####
server=$1

########### Generate Ssh key file for input server name ############
echo "y"|ssh-keygen -C erp@$server -f ~/erpgenerated_pem -N ''

pubkey=`cat ~/erpgenerated_pem.pub`

if [ "$server" == "Erp-Server" ]		### Check for Erp Server
then
	ssh -i ~/.ssh/id_rsa root@erp.theluxuryunlimited.com "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"

elif [ "$server" == "Scrap-Server" ]		### Check for Scrapper Server
then
	for id in 0{1..9} {10..15} 
	do
		ssh -i ~/.ssh/id_rsa root@s$id.theluxuryunlimited.com "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"
	done

elif [ "$server" == "Cropper-Server" ]		### Check for Cropper Server
then
	ssh -i ~/.ssh/id_rsa root@178.62.200.246 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"

elif [ "$server" == "BRANDS" ]
then
	ssh -i ~/.ssh/id_rsa root@138.68.161.55 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"

elif [ "$server" == "AVOIRCHIC" ]
then
	ssh -i ~/.ssh/id_rsa root@138.68.141.190 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"

elif [ "$server" == "OLABELS" ]
then
	ssh -i ~/.ssh/id_rsa root@138.68.185.192 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"

elif [ "$server" == "SOLOLUXURY" ]
then
	ssh -i ~/.ssh/id_rsa root@46.101.78.91 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"

elif [ "$server" == "SUVANDNAT" ]
then
	ssh -i ~/.ssh/id_rsa root@188.166.168.141 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"

elif [ "$server" == "THEFITEDIT" ]
then
	ssh -i ~/.ssh/id_rsa root@139.59.182.8 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"

elif [ "$server" == "THESHADESSHOP" ]
then
	ssh -i ~/.ssh/id_rsa root@138.68.165.128 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"

elif [ "$server" == "UPEAU" ]
then
	ssh -i ~/.ssh/id_rsa root@138.68.181.9 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"

elif [ "$server" == "VERALUSSO" ]
then
	ssh -i ~/.ssh/id_rsa root@139.59.175.99 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"

fi


cat ~/erpgenerated_pem
