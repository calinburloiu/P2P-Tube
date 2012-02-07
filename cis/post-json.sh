#!/usr/bin/env bash

if [ $# -ne 1 ]; then
	echo "usage: $0 json_file" >&2
	exit 1
fi

JSON_FILE="$1"

curl -H application/json --data-binary @"$JSON_FILE" http://localhost:8080/ingest_content
