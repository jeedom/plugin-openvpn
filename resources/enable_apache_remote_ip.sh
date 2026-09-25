#!/bin/bash
# Installs the openvpn plugin apache remoteip configuration and enables mod_remoteip.

set -euo pipefail

SRC="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/apache_remoteip"
CONF_NAME="remoteip"
CONF_AVAILABLE="/etc/apache2/conf-available"
DEST="${CONF_AVAILABLE}/${CONF_NAME}.conf"

if [[ $EUID -ne 0 ]]; then
	echo "This script must be run as root." >&2
	exit 1
fi

if [[ ! -f "$SRC" ]]; then
	echo "Source file not found: $SRC" >&2
	exit 1
fi

install -m 644 -o root -g root "$SRC" "$DEST"
echo "Configuration copied to $DEST"

a2enmod -q remoteip
a2enconf -q "$CONF_NAME"

if apache2ctl configtest; then
	apache2ctl graceful
	echo "Apache reloaded"
else
	echo "Invalid apache configuration, reload aborted." >&2
	exit 1
fi
