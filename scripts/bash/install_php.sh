#!/bin/bash

sudo apt update && sudo apt upgrade -y

sudo apt install -y php-cli php-mbstring php-xml php-curl php-zip php-bcmath unzip curl

php -v
