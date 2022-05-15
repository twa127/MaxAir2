# Autohotspot

This code is based on the work of RaspberryConnect.com, thank you for this guide and code.

The installer has been modified to add support for the Armbian OS and to allow integartion with MaxAir

For Raspian based systems (Raspberry Pi), then Hostapd, dnsmasq and dhcpcd services are installed and configured to implement the HotSpot.

For systems running the NetworkManager service (Armbian) which supports Access Point operation natively, then a HotSpot connection is setup directly using NetworkManager. An 'open' connection is created with a SSID of 'MaxAir', to access the MaxAir GUI interface enter the ip address 10.42.0.1 in the browser. 
