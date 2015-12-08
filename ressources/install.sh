touch /tmp/dependancy_openvpn_in_progress
#!/bin/bash

# Automatically generated script by
# vagrantbox/doc/src/vagrant/src-vagrant/deb2sh.py
# The script is based on packages listed in debpkg_minimal.txt.

#set -x  # make sure each command is printed in the terminal
echo "Launch install of openvpn"
sudo apt-get update
sudo apt-get install -y openvpn

echo "Everything is successfully installed!"
rm /tmp/dependancy_openvpn_in_progress