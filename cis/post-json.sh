#!/usr/bin/env bash

if [ $# -ne 1 ]; then
	echo "usage: $0 json_file" >&2
	exit 1
fi

JSON_FILE="$1"
CIS_URL="http://p2p-next-02.grid.pub.ro:8080/"

curl -H 'Content-Type: application/json' --data-binary @"$JSON_FILE" ${CIS_URL}ingest_content
