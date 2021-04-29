server=$1
echo "y"|ssh-keygen -C erp@$server -f ~/erpgenerated_pem -N ''
pubkey=`cat ~/erpgenerated_pem.pub`

if [ "$server" == "Erp-Server" ]		### Check for Erp Server
then
	sed -i "s%.*erp@$Server%$pubkey%g" .ssh/authorized_keys
elif [ "$server" == "Scrap-Server" ]		### Check for Scrapper Server
then
	for server in 0{1..9} {10..15} 
	do
		ssh -i ~/.ssh/id_rsa root@s$server.theluxuryunlimited.com "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"
	done
elif [ "$server" == "Cropper-Server" ]		### Check for Cropper Server
then
	ssh -i ~/.ssh/id_rsa root@178.62.200.246 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"
elif [ "$server" == "Magento-Server" ]		### Check for Magento Servers
then
	ssh -i ~/.ssh/id_rsa root@138.68.141.190 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"
	ssh -i ~/.ssh/id_rsa root@138.68.161.55 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"
	ssh -i ~/.ssh/id_rsa root@138.68.185.192 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"
	ssh -i ~/.ssh/id_rsa root@46.101.78.91 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"
	ssh -i ~/.ssh/id_rsa root@188.166.168.141 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"
	ssh -i ~/.ssh/id_rsa root@139.59.182.8 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"
	ssh -i ~/.ssh/id_rsa root@138.68.165.128 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"
	ssh -i ~/.ssh/id_rsa root@138.68.181.9 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"
	ssh -i ~/.ssh/id_rsa root@139.59.175.99 "sed -i \"s%.*erp@$server%$pubkey%g\" .ssh/authorized_keys"
fi

echo ~/erpgenerated_pem
