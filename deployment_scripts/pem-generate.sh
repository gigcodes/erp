########### Input Argument for Server Name which need to regenrate pem file #####
server=$1

########### Generate Ssh key file for input server name ############
echo "y"|ssh-keygen -C erp@$server -f ~/erpgenerated_pem -N ''

pubkey=`cat ~/erpgenerated_pem.pub`

if [ "$server" == "Erp-Server" ]		### Check for Erp Server
then
	ssh -i ~/.ssh/id_rsa root@erp.theluxuryunlimited.com "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"

elif [ "$server" == "s01" ] || [ "$server" == "s02" ] || [ "$server" == "s03" ] || [ "$server" == "s04" ] || [ "$server" == "s05" ] || [ "$server" == "s06" ] || [ "$server" == "s07" ] || [ "$server" == "s08" ] || [ "$server" == "s09" ] || [ "$server" == "s10" ] || [ "$server" == "s11" ] || [ "$server" == "s12" ] || [ "$server" == "s13" ] || [ "$server" == "s14" ] || [ "$server" == "s15" ]
then
	ssh -i ~/.ssh/id_rsa root@$server.theluxuryunlimited.com "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"

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
