#!/usr/bin/env bash

set -e

_HOST=front



# create user
_userId=$(curl --silent \
  -d "name=Name&city=Minsk&country=BLR&currency=eur" \
  -X POST "http://$_HOST/api/users" | grep -oP '"id":\K[\d]+')

echo -e "User is $_userId\n"



# make deposit
curl --silent -d "userId=$_userId&currency=eur&amount=10" -X POST "http://$_HOST/api/transactions/deposit"



# check balance
echo -e "Checking balance"
curl --silent "http://$_HOST/api/users/$_userId/balance?currency=eur"



# do withdrawals
echo -e "\n\nStart withdrawals\n"

echo "userFromId=$_userId&currencyFrom=eur&userToId=3&currencyTo=gbp&amount=0.1" > /tmp/ab-test.data

ab -q -n 150 -c 10 \
  -p /tmp/ab-test.data -T application/x-www-form-urlencoded \
  "http://$_HOST/api/transactions/transfer" > /dev/null 2>&1


# check balance
#echo -e "\nCount of failed requests should be 100\n"

echo -e "Checking balance, it should be 0"
curl --silent "http://$_HOST/api/users/$_userId/balance?currency=eur"

