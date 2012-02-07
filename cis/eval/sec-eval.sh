#!/usr/bin/env bash

if [ $# -ne 1 ]; then
	echo "usage: $0 <http | https>" >&2
	exit 1
fi

TEST_TYPE=$1

case $TEST_TYPE in
	"http")
		CMD="curl http://localhost:8080/test"
		;;
	"https")
		CMD="curl https://localhost:8080/test --insecure"
		;;
	*)
		echo "invalid parameter!" >&2
		;;
esac

cd ..

for i in $(seq 1 1000); do
	t1=$(date +%s.%N)
	$CMD 2> /dev/null
	t2=$(date +%s.%N)
	
	delta_t=$(echo "$t2 - $t1" | bc)
	echo $delta_t
done
