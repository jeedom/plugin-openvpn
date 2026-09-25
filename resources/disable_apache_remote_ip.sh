#!/bin/bash
# Disables the Apache remoteip configuration and module.

set -euo pipefail

CONF_NAME="remoteip"

if [[ $EUID -ne 0 ]]; then
	echo "This script must be run as root." >&2
	exit 1
fi

a2disconf -q "$CONF_NAME"
a2dismod -q remoteip

if apache2ctl configtest; then
	apache2ctl graceful
	echo "Apache reloaded"
else
	echo "Invalid apache configuration, reload aborted." >&2
	exit 1
fi
