BRANCH_NAME=$1;
git checkout $BRANCH_NAME;
git pull;
echo $BRANCH_NAME;