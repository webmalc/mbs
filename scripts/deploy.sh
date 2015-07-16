#!/bin/bash
SERVER='root@128.199.55.176'
GREEN='\e[0;32m'
RED='\e[0;31m'
NC='\e[0m'
IGNORE='ignore.txt'
FOLDER='/var/www/mbhs/'
CACHE=$FOLDER'bin/console cache:clear --env=prod'
PROXIES=$FOLDER'bin/console doctrine:mongodb:generate:proxies'
HYDRATORS=$FOLDER'bin/console doctrine:mongodb:generate:hydrators'
FOS=$FOLDER'bin/console fos:js-routing:dump'
ASSEST=$FOLDER'bin/console assets:install '$FOLDER'web --symlink'
ASSESTIC=$FOLDER'bin/console assetic:dump'
PHP_FPM='service php5-fpm restart'

echo -e "${GREEN}Start rsync${NC}"

rsync -avz --delete --exclude-from=scripts/$IGNORE * -e ssh $SERVER:$FOLDER

echo -e "${GREEN}Start clear:cache${NC}"
ssh $SERVER $CACHE

echo -e "${GREEN}Start doctrine:mongodb:generate:hydrators${NC}"
ssh $SERVER $HYDRATORS

echo -e "${GREEN}Start octrine:mongodb:generate:proxies${NC}"
ssh $SERVER $PROXIES

echo -e "${GREEN}Start fos:dump${NC}"
ssh $SERVER $FOS

echo -e "${GREEN}Start assets:install${NC}"
ssh $SERVER $ASSEST

echo -e "${GREEN}Start assetic:dump${NC}"
ssh $SERVER $ASSESTIC

ssh $SERVER $PHP_FPM

