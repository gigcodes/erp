#!/bin/bash

function HELP {
        echo "-r|--repo: Repo Name"
        echo "-s|--scope: Scope"
	echo "-c|--code: Scope Code"
	echo "-p|--path: Path variable"
	echo "-v|--value: Value"
}

args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
                -r|--repo)
                repo="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -s|--scope)
                scope="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -c|--code)
                code="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -p|--path)
                path="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -v|--value)
                value="${args[$((idx+1))]}"
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

### Load environment variables
. /var/www/erp.theluxuryunlimited.com/.env

###### Dump changes from database and push to stage branch ###
cd /opt/magento/$repo
git reset --hard origin/stage
git pull origin stage
composer install
php -f bin/magento -- deploy:mode:set production --skip-compilation
php bin/magento app:config:dump
php bin/magento --lock-env config:set --scope=$scope --scope-code=$code $path $value
php bin/magento app:config:dump
git add app/etc/config.php
git commit -m 'Deployment config erp'
git push origin stage

##### Create PR from stage to master ####
pr=$(hub pull-request -b master -h stage -m "config deployment from erp")
pull_number=$(echo "$pr" | awk -F "/" {'print $7'})

##### Merge PR ####
curl -XPUT -H "Authorization: token $GITHUB_TOKEN" https://api.github.com/repos/ludxb/$repo/pulls/$pull_number/merge
