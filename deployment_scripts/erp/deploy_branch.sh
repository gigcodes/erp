BRANCH_NAME=$1
scriptPath="$(cd "$(dirname "$0")"; pwd)"
cd $scriptPath;
cd ..
git checkout $BRANCH_NAME;
git pull;
echo $BRANCH_NAME;