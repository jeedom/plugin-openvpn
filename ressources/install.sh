#!/bin/bash
touch /tmp/dependancy_openvpn_in_progress
echo 0 > /tmp/dependancy_openvpn_in_progress

echo "Launch install of openvpn"
sudo apt-get update
echo 50 > /tmp/dependancy_openvpn_in_progress
sudo apt-get install -y openvpn
echo 100 > /tmp/dependancy_openvpn_in_progress
echo "Everything is successfully installed!"
rm /tmp/dependancy_openvpn_in_progress