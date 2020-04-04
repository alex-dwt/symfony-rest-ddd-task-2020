#!/usr/bin/env bash

set -e

_RETIES_COUNT=20

function failure {
  echo "Error occured. Balance was not 0"
}

trap failure ERR

for ((i=1; i<=_RETIES_COUNT; i++)); do
  echo -e "Round $i"
  /concurrency_test.bash > /dev/null
done


echo "Success. Everything seems good"
