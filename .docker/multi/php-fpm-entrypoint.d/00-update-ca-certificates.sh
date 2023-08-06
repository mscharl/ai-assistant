#!/bin/sh
set -e

# copy and enable CA if available
if [ -f "/tmp/certificate/ca.crt" ]; then
    cp "/tmp/certificate/ca.crt" "/usr/local/share/ca-certificates/"

    update-ca-certificates
fi
