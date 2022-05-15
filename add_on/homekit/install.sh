#!/bin/bash

#service_name:homebridge.service
#app_name:Apple HomeKit Integration
#app_description:Integrate Apple HomeKit to Enable Siri Voice Control

bash /var/www/api/enable_rewrite.sh

echo "Installing/Updating nodejs"
PROC=$(uname -m)
if [ "${PROC}" == "armv6l" ]; then
        echo 'Version' "${PROC}"
        curl -o node-v11.15.0-linux-armv6l.tar.gz https://nodejs.org/dist/v11.15.0/node-v11.15.0-linux-armv6l.tar.gz
        tar -xzf node-v11.15.0-linux-armv6l.tar.gz
        sudo cp -r node-v11.15.0-linux-armv6l/* /usr/local/
        rm node-v11.15.0-linux-armv6l.tar.gz
        rm -R node-v11.15.0-linux-armv6l
elif [ "${PROC}" == "aarch64" ]; then
        echo 'Version' "${PROC}"
        curl -o node-v15.13.0-linux-arm64.tar.gz https://nodejs.org/dist/v15.13.0/node-v15.13.0-linux-arm64.tar.gz
        tar -xzf node-v15.13.0-linux-arm64.tar.gz
        sudo cp -r node-v15.13.0-linux-arm64/* /usr/local/
        rm node-v15.13.0-linux-arm64.tar.gz
        rm -R node-v15.13.0-linux-arm64
elif [ "${PROC}" == "x86_64" ]; then
        echo 'Version' "${PROC}"
        curl -o node-v15.13.0-linux-x64.tar.gz https://nodejs.org/dist/v15.13.0/node-v15.13.0-linux-x64.tar.gz
        tar -xzf node-v15.13.0-linux-x64.tar.gz
        sudo cp -r node-v15.13.0-linux-x64/* /usr/local/
        rm node-v15.13.0-linux-x64.tar.gz
        rm -R node-v15.13.0-linux-x64
fi

echo "Installing Homebridge"
sudo npm install -g --unsafe-perm homebridge homebridge-config-ui-x

echo "Setup Homebridge Service"
sudo hb-service install --user homebridge

echo "Installing the WebHooks Plugin"
sudo npm install -g homebridge-http-webhooks

echo "Checking For WebHooks Cache Directory"
DIR="/var/lib/homebridge/.node-persist/storage/"
if [ -d "$DIR" ]; then
  echo "Directory Found, Checking if Empty"
  FILE=""
  if [ "$(ls -A $DIR)" ]; then
     echo "Deleting existing Files"
     sudo rm -f -- /var/lib/homebridge/.node-persist/storage/*
  else
    echo "$DIR is Empty"
  fi
else
    echo "Creating Directory, Changing Ownership and Permissions"
    sudo mkdir /var/lib/homebridge/.node-persist
    sudo mkdir $DIR
    sudo chown homebridge:homebridge $DIR
    sudo chmod 755 $DIR
fi
echo "Backing Up and Updateing /var/lib/homebridge/config.json"
echo "Adding WebHooks Plugin and  Accessories for Each Zone"
FILE=/var/lib/homebridge/config.json
sudo cp -a -- "$FILE" "$FILE-$(date +"%Y%m%d-%H%M%S")"
/usr/bin/python3 /var/www/add_on/homekit/config_json.py
echo "Restarting Homebridge"
sudo hb-service restart

# check if Unit File already exists
echo "Checking For Existing Unit File"
FILE=/lib/systemd/system/webhooks_integration.service
if [  -f "$FILE" ]; then
    echo "Deleting Existing Unit File: $FILE"
    rm $FILE
fi
echo "Creating Unit File: $FILE"
sudo cat <<EOT >> /lib/systemd/system/webhooks_integration.service
[Unit]
Description=Homebridge Webhooks Integration
After=multi-user.target
[Service]
Type=simple
WorkingDirectory=/var/www/add_on/homekit
ExecStart=/usr/bin/python3 /var/www/add_on/homekit/webhooks.py
Restart=on-failure
RestartSec=10s
User=root
[Install]
WantedBy=multi-user.target
EOT

echo "Enabling the service"
sudo systemctl daemon-reload
sudo systemctl enable webhooks_integration.service
echo "Starting the service"
sudo systemctl start webhooks_integration.service

