#!/bin/bash

#service_name:pihome_amazon_echo.service
#app_name:Amazon Echo Integration
#app_description:Integrate Amazon Echo Device to Enable Voice Control

bash /var/www/api/enable_rewrite.sh

echo "Check if pip3 is Installed"
var=$(which pip3)
if [ -z "$var" ]
then
  echo "Installing pip3"
  sudo apt-get install python3-pip
else
  echo "pip3 already installed"
fi

echo "Installing Fauxmo"
pip3 install fauxmo

# add unprivileged fauxmo user
echo "Adding Unprivileged Fauxmo User"
sudo useradd -r -s /bin/false fauxmo

echo "Changing Privileges for Fauxmo"
sudo chmod 755 /var/www/add_on/amazon_echo/fauxmo
sudo chown fauxmo:fauxmo fauxmo

confdir="/etc/fauxmo"
### Check for dir, if not found create it using the mkdir ##
[ ! -d "$confdir" ] && mkdir -p "$confdir"
echo "Backing Up and Updateing /etc/fauxmo/config.json"
echo "Adding Accessories for Each Zone"
FILE=/etc/fauxmo/config.json
sudo cp -a -- "$FILE" "$FILE-$(date +"%Y%m%d-%H%M%S")"
sudo /usr/bin/python3 /var/www/add_on/amazon_echo/config_json.py

# check if Unit File already exists
echo "Checking For Existing Unit File"
FILE=/lib/systemd/system/pihome_amazon_echo.service
if [  -f "$FILE" ]; then
    echo "Deleting Existing Unit File: $FILE"
    rm $FILE
fi
echo "Creating Unit File: $FILE"
sudo cat <<EOT >> /lib/systemd/system/pihome_amazon_echo.service
[Unit]
Description=Fauxmo
After=network-online.target
Wants=network-online.target
[Service]
Type=simple
WorkingDirectory=/var/www/add_on/amazon_echo
ExecStart=/var/www/add_on/amazon_echo/fauxmo -c /etc/fauxmo/config.json -v
Restart=on-failure
RestartSec=10s
User=fauxmo
[Install]
WantedBy=multi-user.target
EOT

echo "Enabling the service"
sudo systemctl daemon-reload
sudo systemctl enable pihome_amazon_echo.service
echo "Starting the service"
sudo systemctl start pihome_amazon_echo.service
