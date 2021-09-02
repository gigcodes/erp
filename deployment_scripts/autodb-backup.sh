date=`date +%d-%m-%y`
bkproot=/mnt/backup/erp_backup
bkpdir=/mnt/backup/erp_backup/$date

mkdir -p $bkpdir
mysqldump erp_live |gzip -v > $bkpdir/erp_live.sql.gz

find $bkproot -mtime +8 -exec rm -rf {} \; 


############### Magento Servers Database Backup #############
envfile='/var/www/erp.theluxuryunlimited.com/.env'
magento_servers='BRANDS AVOIRCHIC OLABELS SOLOLUXURY SUVANDNAT THEFITEDIT THESHADESSHOP UPEAU VERALUSSO'
mageroot=/mnt/backup/magento_backup/$date

user=`grep MAGENTO_DB_USER $envfile|cut -d'=' -f2`
pass=`grep MAGENTO_DB_PASSWORD $envfile|cut -d"'" -f2`

for server in $magento_servers
do
	mkdir -p $mageroot/$server
	db=`grep "$server"_DB $envfile|cut -d'=' -f2`
	host=`grep "$server"_HOST $envfile|cut -d'=' -f2`
	mysqldump -h $host -u $user -p"$pass" $db |gzip -v > $mageroot/$server/$db.sql.gz
done
find $mageroot -mtime +8 -exec rm -rf {} \; 
